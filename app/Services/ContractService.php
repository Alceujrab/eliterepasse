<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\ContractSignature;
use App\Models\Document;
use App\Models\Order;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class ContractService
{
    /**
     * Gera um novo contrato a partir de um pedido.
     */
    public function gerarDeOrdem(Order $order, string $template = 'padrao'): Contract
    {
        $user    = $order->user;
        $vehicle = $order->vehicle;

        $contract = Contract::create([
            'order_id'         => $order->id,
            'user_id'          => $order->user_id,
            'vehicle_id'       => $order->vehicle_id,
            'created_by'       => auth()->id(),
            'numero'           => Contract::gerarNumero(),
            'template'         => $template,
            'status'           => 'rascunho',
            'valor_contrato'   => $order->valor_compra,
            'forma_pagamento'  => $order->paymentMethod?->nome,
            'hash_verificacao' => hash('sha256', Str::uuid() . now()->timestamp),

            // Snapshots dos dados no momento da geração
            'dados_comprador' => [
                'name'          => $user?->name,
                'razao_social'  => $user?->razao_social,
                'nome_fantasia' => $user?->nome_fantasia,
                'cnpj'          => $user?->cnpj,
                'email'         => $user?->email,
                'phone'         => $user?->phone,
                'cep'           => $user?->cep,
                'logradouro'    => $user?->logradouro,
                'numero'        => $user?->numero,
                'bairro'        => $user?->bairro,
                'cidade'        => $user?->cidade,
                'estado'        => $user?->estado,
            ],

            'dados_veiculo' => [
                'brand'          => $vehicle?->brand,
                'model'          => $vehicle?->model,
                'version'        => $vehicle?->version,
                'manufacture_year' => $vehicle?->manufacture_year,
                'model_year'     => $vehicle?->model_year,
                'plate'          => $vehicle?->plate,
                'color'          => $vehicle?->color,
                'mileage'        => $vehicle?->mileage,
                'fuel_type'      => $vehicle?->fuel_type,
                'chassis'        => $vehicle?->chassis ?? null,
                'renavam'        => $vehicle?->renavam ?? null,
            ],

            'dados_pagamento' => [
                'metodo'     => $order->paymentMethod?->nome,
                'valor'      => $order->valor_compra,
                'valor_fipe' => $order->valor_fipe,
            ],
        ]);

        // Criar slot de assinatura do comprador (token único para link seguro)
        ContractSignature::create([
            'contract_id'     => $contract->id,
            'user_id'         => $order->user_id,
            'tipo'            => 'comprador',
            'nome'            => $user?->razao_social ?? $user?->name ?? '',
            'documento'       => $user?->cnpj ?? $user?->cpf ?? null,
            'token_assinatura' => Str::random(64),
        ]);

        return $contract;
    }

    /**
     * Registra a assinatura do comprador com geolocalização.
     */
    public function assinarContrato(
        Contract $contract,
        ?float $lat,
        ?float $lng,
        string $assinaturaBase64,
        string $ip,
        string $userAgent
    ): ContractSignature {

        // 1. Geocodificação reversa (apenas se GPS disponível)
        $enderecoGeo = ($lat && $lng)
            ? $this->geocodificacaoReversa($lat, $lng)
            : null;

        // 2. Atualiza o contrato
        $contract->update([
            'status'                => 'assinado',
            'assinado_em'           => now(),
            'lat_assinatura'        => $lat,
            'lng_assinatura'        => $lng,
            'endereco_assinatura'   => $enderecoGeo,
            'ip_assinatura'         => $ip,
            'user_agent_assinatura' => $userAgent,
        ]);

        // 3. Atualiza a assinatura do comprador
        $signature = $contract->assinaturaComprador;
        $signature?->update([
            'assinatura_base64' => $assinaturaBase64,
            'lat'               => $lat,
            'lng'               => $lng,
            'endereco_geo'      => $enderecoGeo,
            'ip'                => $ip,
            'assinado_em'       => now(),
        ]);

        // 4. Marcar veículo como vendido
        $contract->vehicle?->update(['status' => 'sold']);

        // 5. Gerar e salvar PDF Final
        $pdfPath = $this->gerarPdfContrato($contract->fresh(), $signature);

        // 6. Salvar pdf_path no contrato
        $contract->update(['pdf_path' => $pdfPath]);

        // 7. Criar Document visível no portal do cliente
        Document::create([
            'user_id'     => $contract->user_id,
            'vehicle_id'  => $contract->vehicle_id,
            'title'       => "Contrato Assinado {$contract->numero}",
            'file_path'   => $pdfPath,
            'mime_type'   => 'application/pdf',
            'tamanho'     => Storage::disk('local')->exists($pdfPath)
                ? Storage::disk('local')->size($pdfPath)
                : 0,
            'tipo'            => 'contrato_compra',
            'status'          => 'verificado',
            'verificado_em'   => now(),
            'visivel_cliente' => true,
        ]);

        // 8. Registrar no histórico do pedido
        if ($contract->order_id) {
            \App\Models\OrderHistory::registrar(
                $contract->order_id,
                'contrato_assinado',
                null,
                null,
                null,
                $contract->user_id,
                ['contract_id' => $contract->id, 'numero' => $contract->numero]
            );
        }

        // 9. Notificar cliente + admins sobre contrato assinado
        app(NotificationService::class)->contratoAssinado($contract->fresh());

        return $signature;
    }

    /**
     * Gera o PDF do contrato assinado e salva no storage local.
     */
    public function gerarPdfContrato(Contract $contract, ContractSignature $signature): string
    {
        $pdf = Pdf::loadView('pdf.contrato', [
            'contract' => $contract,
            'signature' => $signature,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $fileName = "contratos/{$contract->numero}_" . Str::slug($contract->dados_comprador['razao_social'] ?? $contract->dados_comprador['name']) . ".pdf";
        
        Storage::disk('local')->put($fileName, $pdf->output());

        return $fileName;
    }

    /**
     * Geocodificação reversa via Google Maps ou fallback nominatim.
     */
    public function geocodificacaoReversa(float $lat, float $lng): string
    {
        $apiKey = SystemSetting::get('google_maps_api_key');

        // Google Maps (preferencial)
        if ($apiKey) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'latlng' => "{$lat},{$lng}",
                    'key'    => $apiKey,
                    'language' => 'pt-BR',
                ]);

                $data = $response->json();

                if ($data['status'] === 'OK' && ! empty($data['results'])) {
                    return $data['results'][0]['formatted_address'];
                }
            } catch (\Exception $e) {
                Log::warning('ContractService: Google Maps geocoding failed', ['error' => $e->getMessage()]);
            }
        }

        // Fallback: Nominatim (OpenStreetMap) — sem necessidade de API key
        try {
            $response = Http::withHeaders(['User-Agent' => 'EliteRepasse/1.0'])
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat'    => $lat,
                    'lon'    => $lng,
                    'format' => 'json',
                ]);

            $data = $response->json();
            return $data['display_name'] ?? "Lat: {$lat}, Lng: {$lng}";
        } catch (\Exception $e) {
            return "Lat: {$lat}, Lng: {$lng}";
        }
    }

    /**
     * Envia link de assinatura via WhatsApp.
     */
    public function enviarLinkAssinatura(Contract $contract): bool
    {
        $signature = $contract->assinaturaComprador;

        if (! $signature) return false;

        $url    = url("/contrato/assinar/{$signature->token_assinatura}");
        $nome   = $contract->dados_comprador['razao_social'] ?? $contract->dados_comprador['name'] ?? 'Cliente';
        $numero = $contract->numero;
        $veiculo = implode(' ', [
            $contract->dados_veiculo['brand'] ?? '',
            $contract->dados_veiculo['model'] ?? '',
            $contract->dados_veiculo['model_year'] ?? '',
        ]);

        $mensagem = "✍️ *Portal Elite Repasse*\n\nOlá, {$nome}!\n\nSeu contrato de compra nº *{$numero}* está aguardando sua assinatura digital.\n\n🚗 Veículo: *{$veiculo}*\n💰 Valor: *R$ " . number_format((float) $contract->valor_contrato, 2, ',', '.') . "*\n\n👇 Clique para assinar:\n{$url}\n\n_Este link expira em 72 horas._";

        try {
            $instance = \App\Models\EvolutionInstance::getPadrao();
            $user     = $contract->user;
            $phone    = $user?->phone ?? null;

            if ($instance && $phone) {
                $phone = preg_replace('/\D/', '', $phone);
                if (strlen($phone) <= 11) $phone = '55' . $phone;
                $instance->sendText($phone, $mensagem);
            }

            $contract->update(['status' => 'aguardando', 'enviado_em' => now()]);
            return true;
        } catch (\Exception $e) {
            Log::error('ContractService: Falha ao enviar WhatsApp', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
