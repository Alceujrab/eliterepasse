<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleOAuthController extends Controller
{
    /**
     * Redireciona o usuário para o Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Recebe o callback após login no Google.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('GoogleOAuth falhou ao tentar capturar usuário: ' . $e->getMessage());
            return redirect()->route('login')->with('status', 'Falha ao autenticar com Google. Tente novamente.');
        }

        // Tenta encontrar um usuário pelo campo google_id
        $user = User::where('google_id', $googleUser->id)->first();

        // Se não encontrar por google_id, tenta encontrar pelo email (caso o usuário já exista e logou pela primeira vez via Google)
        if (! $user) {
            $user = User::where('email', $googleUser->email)->first();

            if ($user) {
                // Atualiza o google_id do usuário existente
                $user->update([
                    'google_id'    => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
            } else {
                // Cria um novo usuário pendente
                $user = User::create([
                    'name'         => $googleUser->name,
                    'email'        => $googleUser->email,
                    'password'     => Hash::make(Str::random(24)), // Senha dummy, pois o login é via Google
                    'google_id'    => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'status'       => 'pendente',
                    // Note que faltam dados cruciais como CPF, CNPJ, Razão Social, etc. 
                    // Isso exigirá que ele termine o cadastro ou um fluxo de onboarding.
                ]);
            }
        } else {
            // Se já encontrou pelo google_id, atualiza o token
            $user->update([
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
            ]);
        }

        Auth::login($user, true);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
