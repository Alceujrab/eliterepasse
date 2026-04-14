<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailTemplatesIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $active = $request->string('active')->toString();

        $queryFactory = function () use ($search, $active) {
            return EmailTemplate::query()
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery
                            ->where('nome', 'like', "%{$search}%")
                            ->orWhere('slug', 'like', "%{$search}%")
                            ->orWhere('assunto', 'like', "%{$search}%");
                    });
                })
                ->when($active !== '', fn ($query) => $query->where('ativo', $active === '1'));
        };

        $templates = $queryFactory()->orderBy('nome')->paginate(15)->withQueryString();

        $summary = [
            'filteredTotal' => $templates->total(),
            'active' => $queryFactory()->where('ativo', true)->count(),
            'inactive' => $queryFactory()->where('ativo', false)->count(),
            'system' => $queryFactory()->get()->filter(fn (EmailTemplate $template) => $template->isSystemTemplate())->count(),
        ];

        return view('admin.email-templates.index', [
            'templates' => $templates,
            'search' => $search,
            'active' => $active,
            'summary' => $summary,
            'globalTotalTemplates' => EmailTemplate::count(),
            'hasActiveFilters' => $search !== '' || $active !== '',
        ]);
    }
}