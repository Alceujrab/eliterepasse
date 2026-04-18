<?php

namespace App\Support\Filters;

use Illuminate\Http\Request;

/**
 * Persiste filtros simples (string|int) na sessao por chave de tela.
 *
 * Uso:
 *   $filters = (new RememberedFilters($request, 'admin.orders.index'))
 *       ->remember(['status', 'q', 'sort', 'per_page']);
 *
 *   $status = $filters->get('status', '');
 *
 * Limpeza: ?reset=1 na URL apaga os filtros memorizados da tela.
 */
class RememberedFilters
{
    /** @var array<string, mixed> */
    protected array $values = [];

    public function __construct(protected Request $request, protected string $key)
    {
    }

    /**
     * Le os filtros pedidos do request, persiste na sessao e expoe leitura.
     *
     * @param  array<int, string>  $fields
     * @return $this
     */
    public function remember(array $fields): self
    {
        $sessionKey = $this->sessionKey();

        if ($this->request->boolean('reset')) {
            $this->request->session()->forget($sessionKey);
        }

        $stored = (array) $this->request->session()->get($sessionKey, []);
        $next = $stored;
        $touched = $this->request->boolean('reset');

        foreach ($fields as $field) {
            if ($this->request->has($field)) {
                $value = $this->request->input($field);
                $value = is_string($value) ? trim($value) : $value;

                if ($value === '' || $value === null) {
                    unset($next[$field]);
                } else {
                    $next[$field] = $value;
                }
                $touched = true;
            } elseif (array_key_exists($field, $stored)) {
                // Reaplicar valor armazenado no request para que a view enxergue.
                $this->request->merge([$field => $stored[$field]]);
            }
        }

        if ($touched) {
            $this->request->session()->put($sessionKey, $next);
        }

        $this->values = $next;

        return $this;
    }

    public function get(string $field, mixed $default = null): mixed
    {
        if ($this->request->has($field)) {
            $value = $this->request->input($field);
            return is_string($value) ? trim($value) : $value;
        }

        return $this->values[$field] ?? $default;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->values;
    }

    public function clearUrl(): string
    {
        return $this->request->url() . '?reset=1';
    }

    protected function sessionKey(): string
    {
        return 'admin_filters.' . $this->key;
    }
}
