<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralDocument;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GeneralDocumentsIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $status = $request->string('status')->toString();

        $documents = GeneralDocument::query()
            ->when($search !== '', fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('description', 'like', "%{$search}%"))
            ->when($status === 'ativo', fn ($q) => $q->where('is_active', true))
            ->when($status === 'inativo', fn ($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'total' => GeneralDocument::count(),
            'ativos' => GeneralDocument::where('is_active', true)->count(),
            'inativos' => GeneralDocument::where('is_active', false)->count(),
        ];

        return view('admin.general-documents.index', [
            'documents' => $documents,
            'search' => $search,
            'status' => $status,
            'summary' => $summary,
            'hasActiveFilters' => $search !== '' || $status !== '',
        ]);
    }
}
