<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user) {
            return $next($request);
        }

        // Admin sempre passa
        if ($user->is_admin) {
            return $next($request);
        }

        // Bloqueado — desloga e redireciona
        if ($user->status === 'bloqueado') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'email' => 'Sua conta foi suspensa. Entre em contato com o suporte.',
            ]);
        }

        // Pendente — redireciona para página de espera
        if ($user->status === 'pendente') {
            // Permite acesso apenas a logout e à própria página de espera
            if (! $request->routeIs('aguardando.aprovacao', 'logout')) {
                return redirect()->route('aguardando.aprovacao');
            }
        }

        return $next($request);
    }
}
