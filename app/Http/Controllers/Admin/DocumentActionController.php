<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tipo' => ['required', 'in:' . implode(',', array_keys(Document::tipoLabels()))],
            'titulo' => ['required', 'string', 'max:255'],
            'user_id' => ['nullable', 'exists:users,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'validade' => ['nullable', 'date'],
            'status' => ['required', 'in:' . implode(',', array_keys(Document::statusLabels()))],
            'visivel_cliente' => ['nullable', 'boolean'],
            'observacoes' => ['nullable', 'string'],
            'arquivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $file = $validated['arquivo'];
        $path = $file->store('documentos', 'public');

        Document::create([
            'tipo' => $validated['tipo'],
            'titulo' => $validated['titulo'],
            'user_id' => $validated['user_id'] ?? null,
            'vehicle_id' => $validated['vehicle_id'] ?? null,
            'validade' => $validated['validade'] ?? null,
            'status' => $validated['status'],
            'visivel_cliente' => $request->boolean('visivel_cliente'),
            'observacoes' => $validated['observacoes'] ?? null,
            'file_path' => $path,
            'nome_original' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'tamanho' => $file->getSize(),
            'verificado_por' => in_array($validated['status'], ['verificado', 'rejeitado'], true) ? Auth::id() : null,
            'verificado_em' => in_array($validated['status'], ['verificado', 'rejeitado'], true) ? now() : null,
        ]);

        return back()->with('admin_success', 'Documento cadastrado no novo painel.');
    }

    public function verify(Document $document, NotificationService $notificationService): RedirectResponse
    {
        if ($document->status !== 'pendente') {
            return back()->with('admin_warning', 'Documento fora do estado permitido para verificação.');
        }

        $document->update([
            'status' => 'verificado',
            'motivo_rejeicao' => null,
            'verificado_por' => Auth::id(),
            'verificado_em' => now(),
        ]);

        $notificationService->documentoVerificado($document->fresh(['user', 'vehicle']));

        return back()->with('admin_success', 'Documento verificado com sucesso.');
    }

    public function reject(Request $request, Document $document, NotificationService $notificationService): RedirectResponse
    {
        if (! in_array($document->status, ['pendente', 'verificado'], true)) {
            return back()->with('admin_warning', 'Documento fora do estado permitido para rejeição.');
        }

        $validated = $request->validate([
            'motivo_rejeicao' => ['required', 'string', 'max:1000'],
        ]);

        $document->update([
            'status' => 'rejeitado',
            'motivo_rejeicao' => $validated['motivo_rejeicao'],
            'verificado_por' => Auth::id(),
            'verificado_em' => now(),
        ]);

        $notificationService->documentoVerificado($document->fresh(['user', 'vehicle']));

        return back()->with('admin_warning', 'Documento rejeitado e cliente notificado.');
    }
}