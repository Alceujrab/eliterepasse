<?php

namespace App\Support\Tables;

use Illuminate\Support\Facades\Request;

/**
 * Helper para gerar links de cabecalho de tabela com ordenacao.
 *
 * Uso na view:
 *   {!! \App\Support\Tables\SortableHeader::link('numero', 'Numero', $sort, $direction) !!}
 *
 * No controller, validar contra whitelist:
 *   $sort = SortableHeader::sanitize($request->input('sort'), ['numero','created_at'], 'created_at');
 *   $direction = SortableHeader::direction($request->input('direction'));
 */
class SortableHeader
{
    /**
     * @param  array<int, string>  $allowed
     */
    public static function sanitize(?string $value, array $allowed, string $default): string
    {
        return in_array($value, $allowed, true) ? $value : $default;
    }

    public static function direction(?string $value): string
    {
        return strtolower((string) $value) === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Gera o link HTML do cabecalho com indicador visual.
     */
    public static function link(string $field, string $label, string $currentSort, string $currentDirection, array $extraQuery = []): string
    {
        $isActive = $currentSort === $field;
        $nextDirection = $isActive && $currentDirection === 'asc' ? 'desc' : 'asc';

        $query = array_merge(Request::query(), $extraQuery, [
            'sort' => $field,
            'direction' => $nextDirection,
            'page' => 1,
        ]);
        unset($query['reset']);
        $url = Request::url() . '?' . http_build_query($query);

        $arrow = '';
        if ($isActive) {
            $arrow = $currentDirection === 'asc'
                ? ' <span aria-hidden="true">&#9650;</span>'
                : ' <span aria-hidden="true">&#9660;</span>';
        } else {
            $arrow = ' <span aria-hidden="true" class="opacity-30">&#8645;</span>';
        }

        $aria = $isActive ? ' aria-sort="' . ($currentDirection === 'asc' ? 'ascending' : 'descending') . '"' : '';
        $cls = 'inline-flex items-center gap-1 font-extrabold uppercase tracking-[0.12em] text-xs text-slate-600 hover:text-blue-600';

        return '<a href="' . e($url) . '"' . $aria . ' class="' . $cls . '">' . e($label) . $arrow . '</a>';
    }
}
