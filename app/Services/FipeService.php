<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Serviço de integração com a Tabela FIPE.
 *
 * API pública: https://parallelum.com.br/fipe/api/v2
 * Documentação: https://deividfortuna.github.io/fipe/
 *
 * Endpoints:
 *   GET /cars/brands                               → marcas de carros
 *   GET /cars/brands/{brandId}/models              → modelos de uma marca
 *   GET /cars/brands/{brandId}/models/{modelId}/years → anos disponíveis
 *   GET /cars/brands/{brandId}/models/{modelId}/years/{yearId} → preço FIPE
 *
 * Tipos: cars | trucks | motorcycles
 */
class FipeService
{
    private const BASE_URL  = 'https://parallelum.com.br/fipe/api/v2';
    private const CACHE_TTL = 86400; // 24 horas

    // ─── Marcas ──────────────────────────────────────────────────────
    public function getMarcas(string $tipo = 'cars'): array
    {
        return Cache::remember("fipe.{$tipo}.marcas", self::CACHE_TTL, function () use ($tipo) {
            $response = Http::get(self::BASE_URL . "/{$tipo}/brands");

            if ($response->failed()) {
                Log::warning('FipeService: Falha ao buscar marcas', ['tipo' => $tipo]);
                return [];
            }

            return collect($response->json())
                ->map(fn ($m) => ['code' => $m['code'], 'name' => $m['name']])
                ->sortBy('name')
                ->values()
                ->toArray();
        });
    }

    /** Retorna array [code => name] para uso em selects */
    public function getMarcasOptions(string $tipo = 'cars'): array
    {
        return collect($this->getMarcas($tipo))
            ->pluck('name', 'code')
            ->toArray();
    }

    // ─── Modelos ─────────────────────────────────────────────────────
    public function getModelos(string $brandCode, string $tipo = 'cars'): array
    {
        return Cache::remember("fipe.{$tipo}.modelos.{$brandCode}", self::CACHE_TTL, function () use ($brandCode, $tipo) {
            $response = Http::get(self::BASE_URL . "/{$tipo}/brands/{$brandCode}/models");

            if ($response->failed()) return [];

            return collect($response->json())
                ->map(fn ($m) => ['code' => $m['code'], 'name' => $m['name']])
                ->sortBy('name')
                ->values()
                ->toArray();
        });
    }

    public function getModelosOptions(string $brandCode, string $tipo = 'cars'): array
    {
        return collect($this->getModelos($brandCode, $tipo))
            ->pluck('name', 'code')
            ->toArray();
    }

    // ─── Anos ────────────────────────────────────────────────────────
    public function getAnos(string $brandCode, string $modelCode, string $tipo = 'cars'): array
    {
        return Cache::remember("fipe.{$tipo}.anos.{$brandCode}.{$modelCode}", self::CACHE_TTL, function () use ($brandCode, $modelCode, $tipo) {
            $response = Http::get(self::BASE_URL . "/{$tipo}/brands/{$brandCode}/models/{$modelCode}/years");

            if ($response->failed()) return [];

            return collect($response->json())
                ->map(fn ($a) => ['code' => $a['code'], 'name' => $a['name']])
                ->toArray();
        });
    }

    public function getAnosOptions(string $brandCode, string $modelCode, string $tipo = 'cars'): array
    {
        return collect($this->getAnos($brandCode, $modelCode, $tipo))
            ->pluck('name', 'code')
            ->toArray();
    }

    // ─── Preço FIPE ──────────────────────────────────────────────────
    public function getPreco(string $brandCode, string $modelCode, string $yearCode, string $tipo = 'cars'): ?array
    {
        $key = "fipe.{$tipo}.preco.{$brandCode}.{$modelCode}.{$yearCode}";

        return Cache::remember($key, self::CACHE_TTL, function () use ($brandCode, $modelCode, $yearCode, $tipo) {
            $response = Http::get(self::BASE_URL . "/{$tipo}/brands/{$brandCode}/models/{$modelCode}/years/{$yearCode}");

            if ($response->failed()) return null;

            $data = $response->json();

            return [
                'codigo_fipe'    => $data['codFipe'] ?? null,
                'marca'          => $data['brand'] ?? null,
                'modelo'         => $data['model'] ?? null,
                'ano'            => $data['modelYear'] ?? null,
                'combustivel'    => $data['fuel'] ?? null,
                'preco_str'      => $data['price'] ?? null,
                'preco'          => $this->parsePrecoBrl($data['price'] ?? '0'),
                'referencia_mes' => $data['referenceMonth'] ?? null,
            ];
        });
    }

    /** Busca preço por código FIPE direto */
    public function getPrecoByCodigoFipe(string $codigoFipe, string $tipo = 'cars'): ?array
    {
        // Tenta encontrar através de uma busca por todas as marcas
        // Alternativa: usar endpoint público do FIPE
        try {
            $response = Http::get("https://fipe.parallelum.com.br/api/v1/{$tipo}/marcas");
            if ($response->failed()) return null;

            // Cache da busca por código não é viável sem iterar — usar endpoint v1
            $response2 = Http::get("https://fipe.parallelum.com.br/api/v1/{$tipo}/marcas/1/modelos/1/anos/2022-3");
            // Fallback: retornar null e usuário informa manualmente
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    // ─── Busca rápida por marca/modelo/ano texto ────────────────────
    /**
     * Tenta buscar automaticamente o preço FIPE a partir de
     * texto de marca e modelo (busca best-match).
     */
    public function buscarAutomatico(string $marca, string $modelo, int $ano, string $tipo = 'cars'): ?array
    {
        try {
            $marcas = $this->getMarcas($tipo);
            $marcaNorm = $this->normalizar($marca);

            $marcaMatch = collect($marcas)->first(fn ($m) => str_contains(
                $this->normalizar($m['name']), $marcaNorm
            ));

            if (! $marcaMatch) return null;

            $modelos = $this->getModelos($marcaMatch['code'], $tipo);
            $modeloNorm = $this->normalizar($modelo);

            $modeloMatch = collect($modelos)->first(fn ($m) => str_contains(
                $this->normalizar($m['name']), $modeloNorm
            ));

            if (! $modeloMatch) return null;

            $anos = $this->getAnos($marcaMatch['code'], $modeloMatch['code'], $tipo);
            $anoMatch = collect($anos)->first(fn ($a) => str_starts_with($a['code'], (string) $ano));

            if (! $anoMatch) return null;

            return $this->getPreco($marcaMatch['code'], $modeloMatch['code'], $anoMatch['code'], $tipo);
        } catch (\Exception $e) {
            Log::warning('FipeService: Busca automática falhou', ['error' => $e->getMessage()]);
            return null;
        }
    }

    // ─── Helpers ─────────────────────────────────────────────────────
    private function parsePrecoBrl(string $preco): float
    {
        // "R$ 85.000,00" → 85000.00
        $limpo = preg_replace('/[^0-9,]/', '', $preco);
        $limpo = str_replace(',', '.', $limpo);
        // Remove pontos de milhar (ex.: 85.000 → 85000)
        if (substr_count($limpo, '.') > 1) {
            $limpo = str_replace('.', '', substr($limpo, 0, -3)) . '.' . substr($limpo, -2);
        }
        return (float) $limpo;
    }

    private function normalizar(string $texto): string
    {
        return strtolower(
            iconv('UTF-8', 'ASCII//TRANSLIT', $texto)
        );
    }
}
