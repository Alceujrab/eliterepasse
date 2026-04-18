<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $login = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    public ?string $recaptcha_token = null;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $loginType = filter_var($this->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'cpf';

        // Remove mascara se for considerado CPF
        $loginValue = $loginType === 'cpf' ? preg_replace('/[^0-9]/', '', $this->login) : $this->login;

        // Validar reCAPTCHA
        if (config('services.recaptcha.secret_key') && \App\Models\SystemSetting::get('google_recaptcha_ativo')) {
            if (empty($this->recaptcha_token)) {
                \Illuminate\Support\Facades\Log::warning('Login: reCAPTCHA token ausente', ['login' => $this->login, 'ip' => request()->ip()]);
                throw ValidationException::withMessages([
                    'form.login' => 'Nao foi possivel verificar a seguranca do navegador. Recarregue a pagina (Ctrl+F5) e tente novamente.',
                ]);
            }

            try {
                $response = \Illuminate\Support\Facades\Http::asForm()->timeout(8)->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret'   => config('services.recaptcha.secret_key'),
                    'response' => $this->recaptcha_token,
                    'remoteip' => request()->ip(),
                ]);
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Login: falha ao contatar reCAPTCHA', ['error' => $e->getMessage()]);
                throw ValidationException::withMessages([
                    'form.login' => 'Servico de verificacao de seguranca indisponivel. Tente novamente em alguns instantes.',
                ]);
            }

            $score = (float) ($response->json('score') ?? 0);
            if (! $response->json('success')) {
                \Illuminate\Support\Facades\Log::warning('Login: reCAPTCHA invalido', [
                    'login' => $this->login,
                    'errors' => $response->json('error-codes'),
                    'ip' => request()->ip(),
                ]);
                throw ValidationException::withMessages([
                    'form.login' => 'A verificacao de seguranca expirou. Recarregue a pagina e tente novamente.',
                ]);
            }

            if ($score < 0.5) {
                \Illuminate\Support\Facades\Log::warning('Login: reCAPTCHA score baixo', [
                    'login' => $this->login,
                    'score' => $score,
                    'ip' => request()->ip(),
                ]);
                throw ValidationException::withMessages([
                    'form.login' => 'Nao foi possivel confirmar que voce e humano. Atualize a pagina, evite navegacao anonima ou tente outro navegador.',
                ]);
            }
        }

        if (! Auth::attempt([$loginType => $loginValue, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.login' => 'Login ou senha incorretos. Verifique os dados e tente novamente.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.login' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->login).'|'.request()->ip());
    }
}
