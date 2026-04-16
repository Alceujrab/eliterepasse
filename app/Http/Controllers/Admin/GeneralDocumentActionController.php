<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GeneralDocumentActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'arquivo' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $file = $request->file('arquivo');
        $path = $file->store('general-documents', 'public');

        GeneralDocument::create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'file_path' => $path,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('admin_success', 'Documento geral cadastrado com sucesso.');
    }

    public function update(Request $request, GeneralDocument $generalDocument): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'arquivo' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx', 'max:10240'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ];

        if ($request->hasFile('arquivo')) {
            $file = $request->file('arquivo');
            $data['file_path'] = $file->store('general-documents', 'public');
        }

        $generalDocument->update($data);

        return back()->with('admin_success', 'Documento geral atualizado.');
    }

    public function destroy(GeneralDocument $generalDocument): RedirectResponse
    {
        $generalDocument->delete();

        return back()->with('admin_success', 'Documento geral removido.');
    }
}
