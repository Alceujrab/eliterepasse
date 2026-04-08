<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\FipeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FipeController extends Controller
{
    public function __construct(private readonly FipeService $fipe) {}

    /** GET /api/fipe/marcas?tipo=cars */
    public function marcas(Request $request): JsonResponse
    {
        $tipo = $request->get('tipo', 'cars');
        return response()->json($this->fipe->getMarcas($tipo));
    }

    /** GET /api/fipe/modelos?marca_id=58&tipo=cars */
    public function modelos(Request $request): JsonResponse
    {
        $marcaId = $request->get('marca_id');
        $tipo    = $request->get('tipo', 'cars');

        if (! $marcaId) {
            return response()->json(['error' => 'marca_id é obrigatório'], 422);
        }

        return response()->json($this->fipe->getModelos($marcaId, $tipo));
    }

    /** GET /api/fipe/anos?marca_id=58&modelo_id=1234&tipo=cars */
    public function anos(Request $request): JsonResponse
    {
        $marcaId  = $request->get('marca_id');
        $modeloId = $request->get('modelo_id');
        $tipo     = $request->get('tipo', 'cars');

        if (! $marcaId || ! $modeloId) {
            return response()->json(['error' => 'marca_id e modelo_id são obrigatórios'], 422);
        }

        return response()->json($this->fipe->getAnos($marcaId, $modeloId, $tipo));
    }

    /** GET /api/fipe/preco?marca_id=58&modelo_id=1234&ano_id=2022-3&tipo=cars */
    public function preco(Request $request): JsonResponse
    {
        $marcaId  = $request->get('marca_id');
        $modeloId = $request->get('modelo_id');
        $anoId    = $request->get('ano_id');
        $tipo     = $request->get('tipo', 'cars');

        if (! $marcaId || ! $modeloId || ! $anoId) {
            return response()->json(['error' => 'Parâmetros incompletos'], 422);
        }

        $preco = $this->fipe->getPreco($marcaId, $modeloId, $anoId, $tipo);

        if (! $preco) {
            return response()->json(['error' => 'Preço não encontrado na tabela FIPE'], 404);
        }

        return response()->json($preco);
    }

    /** POST /api/fipe/buscar-automatico — busca por texto livre */
    public function buscarAutomatico(Request $request): JsonResponse
    {
        $request->validate([
            'marca'  => 'required|string',
            'modelo' => 'required|string',
            'ano'    => 'required|integer',
            'tipo'   => 'nullable|in:cars,trucks,motorcycles',
        ]);

        $preco = $this->fipe->buscarAutomatico(
            $request->marca,
            $request->modelo,
            (int) $request->ano,
            $request->get('tipo', 'cars')
        );

        if (! $preco) {
            return response()->json([
                'success' => false,
                'message' => 'Veículo não encontrado na tabela FIPE. Informe os dados manualmente.',
            ], 404);
        }

        return response()->json(['success' => true, 'data' => $preco]);
    }
}
