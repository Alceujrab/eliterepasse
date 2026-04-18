<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\UserBlockedNotification;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ClientActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'razao_social'       => ['required', 'string', 'max:255'],
            'nome_fantasia'      => ['nullable', 'string', 'max:255'],
            'cnpj'               => ['nullable', 'string', 'max:20', 'unique:users,cnpj'],
            'inscricao_estadual' => ['nullable', 'string', 'max:20'],
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'              => ['nullable', 'string', 'max:20'],
            'cpf'                => ['nullable', 'string', 'max:14'],
            'cep'                => ['nullable', 'string', 'max:10'],
            'logradouro'         => ['nullable', 'string', 'max:255'],
            'numero'             => ['nullable', 'string', 'max:20'],
            'complemento'        => ['nullable', 'string', 'max:255'],
            'bairro'             => ['nullable', 'string', 'max:100'],
            'cidade'             => ['nullable', 'string', 'max:100'],
            'estado'             => ['nullable', 'string', 'max:2'],
            'status'             => ['required', 'in:pendente,ativo,bloqueado'],
            'password'           => ['required', 'string', 'min:6'],
            'observacoes'        => ['nullable', 'string', 'max:2000'],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_admin'] = false;

        if ($validated['status'] === 'ativo') {
            $validated['aprovado_em'] = now();
            $validated['aprovado_por'] = Auth::id();
        }

        $client = User::create($validated);

        return redirect()
            ->route('admin.v2.clients.show', $client)
            ->with('admin_success', 'Cliente cadastrado com sucesso.');
    }

    public function update(Request $request, User $client): RedirectResponse
    {
        abort_if($client->is_admin, 404);

        $validated = $request->validate([
            'razao_social'       => ['required', 'string', 'max:255'],
            'nome_fantasia'      => ['nullable', 'string', 'max:255'],
            'cnpj'               => ['nullable', 'string', 'max:20', Rule::unique('users', 'cnpj')->ignore($client->id)],
            'inscricao_estadual' => ['nullable', 'string', 'max:20'],
            'name'               => ['required', 'string', 'max:255'],
            'email'              => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($client->id)],
            'phone'              => ['nullable', 'string', 'max:20'],
            'cpf'                => ['nullable', 'string', 'max:14'],
            'cep'                => ['nullable', 'string', 'max:10'],
            'logradouro'         => ['nullable', 'string', 'max:255'],
            'numero'             => ['nullable', 'string', 'max:20'],
            'complemento'        => ['nullable', 'string', 'max:255'],
            'bairro'             => ['nullable', 'string', 'max:100'],
            'cidade'             => ['nullable', 'string', 'max:100'],
            'estado'             => ['nullable', 'string', 'max:2'],
            'status'             => ['required', 'in:pendente,ativo,bloqueado'],
            'password'           => ['nullable', 'string', 'min:6'],
            'observacoes'        => ['nullable', 'string', 'max:2000'],
        ]);

        // Se trocou para ativo e ainda não tinha aprovação
        if ($validated['status'] === 'ativo' && ! $client->aprovado_em) {
            $validated['aprovado_em'] = now();
            $validated['aprovado_por'] = Auth::id();
        }

        // Senha: atualizar apenas se preenchida
        if (filled($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $client->update($validated);

        return redirect()
            ->route('admin.v2.clients.show', $client)
            ->with('admin_success', 'Dados do cliente atualizados com sucesso.');
    }

    public function approve(User $client, NotificationService $notificationService): RedirectResponse
    {
        if ($client->is_admin) {
            abort(404);
        }

        if ($client->status === 'ativo') {
            return back()->with('admin_warning', 'Este cliente ja esta aprovado.');
        }

        DB::transaction(function () use ($client, $notificationService) {
            $client->update([
                'status' => 'ativo',
                'aprovado_em' => now(),
                'aprovado_por' => Auth::id(),
            ]);

            $notificationService->clienteAprovado($client->fresh());
        });

        return back()->with('admin_success', 'Cliente aprovado e notificado com sucesso.');
    }

    public function block(User $client): RedirectResponse
    {
        if ($client->is_admin) {
            abort(404);
        }

        if ($client->status === 'bloqueado') {
            return back()->with('admin_warning', 'Este cliente ja esta bloqueado.');
        }

        DB::transaction(function () use ($client) {
            $client->update(['status' => 'bloqueado']);
            $client->notify(new UserBlockedNotification());
        });

        return back()->with('admin_success', 'Acesso do cliente bloqueado.');
    }
}