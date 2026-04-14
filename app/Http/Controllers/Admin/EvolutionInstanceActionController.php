<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EvolutionInstance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EvolutionInstanceActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateInstance($request);

        if ($request->boolean('padrao')) {
            EvolutionInstance::query()->update(['padrao' => false]);
        }

        $instance = EvolutionInstance::create([
            ...$validated,
            'url_base' => rtrim($validated['url_base'], '/'),
            'ativo' => $request->boolean('ativo'),
            'padrao' => $request->boolean('padrao'),
            'status_conexao' => 'close',
        ]);

        return redirect()->route('admin.v2.whatsapp-instancias.show', $instance)
            ->with('admin_success', 'Instancia cadastrada com sucesso.');
    }

    public function update(Request $request, EvolutionInstance $evolutionInstance): RedirectResponse
    {
        $validated = $this->validateInstance($request);

        if ($request->boolean('padrao')) {
            EvolutionInstance::whereKeyNot($evolutionInstance->id)->update(['padrao' => false]);
        }

        $evolutionInstance->update([
            ...$validated,
            'url_base' => rtrim($validated['url_base'], '/'),
            'ativo' => $request->boolean('ativo'),
            'padrao' => $request->boolean('padrao'),
        ]);

        return back()->with('admin_success', 'Instancia atualizada com sucesso.');
    }

    public function testConnection(EvolutionInstance $evolutionInstance): RedirectResponse
    {
        $connected = $evolutionInstance->testarConexao();

        return back()->with(
            $connected ? 'admin_success' : 'admin_warning',
            $connected
                ? "Instancia {$evolutionInstance->nome} conectada com sucesso."
                : "Nao foi possivel validar a conexao da instancia {$evolutionInstance->nome}."
        );
    }

    public function logout(EvolutionInstance $evolutionInstance): RedirectResponse
    {
        $ok = $evolutionInstance->logout();

        return back()->with(
            $ok ? 'admin_success' : 'admin_warning',
            $ok
                ? "Instancia {$evolutionInstance->nome} desconectada."
                : "Nao foi possivel desconectar {$evolutionInstance->nome}."
        );
    }

    public function sendTest(Request $request, EvolutionInstance $evolutionInstance): RedirectResponse
    {
        $validated = $request->validate([
            'telefone' => ['required', 'string', 'min:10', 'max:20'],
        ]);

        $result = $evolutionInstance->sendText(
            $validated['telefone'],
            "🧪 *Teste de Integracao — Elite Repasse*\n\nMensagem enviada pelo Admin v2.\n\n_Instancia: {$evolutionInstance->nome}_"
        );

        return back()->with(
            ($result['success'] ?? false) ? 'admin_success' : 'admin_warning',
            ($result['success'] ?? false)
                ? 'Mensagem de teste enviada com sucesso.'
                : 'Falha ao enviar mensagem de teste: ' . ($result['error'] ?? 'erro desconhecido')
        );
    }

    private function validateInstance(Request $request): array
    {
        return $request->validate([
            'nome' => ['required', 'string', 'max:80'],
            'instancia' => ['required', 'string', 'max:120'],
            'url_base' => ['required', 'url', 'max:255'],
            'api_key' => ['required', 'string'],
        ]);
    }
}