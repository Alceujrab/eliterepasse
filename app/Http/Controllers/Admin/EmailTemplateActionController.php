<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Services\GeminiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmailTemplateActionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateTemplate($request, null);

        $validated['variaveis_disponiveis'] = $this->normalizeVariables($validated['variaveis_disponiveis'] ?? []);
        $validated['ativo'] = $request->boolean('ativo');

        $template = EmailTemplate::create($validated);

        return redirect()->route('admin.v2.email-templates.show', $template)
            ->with('admin_success', 'Template criado com sucesso.');
    }

    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $this->validateTemplate($request, $emailTemplate);

        if ($emailTemplate->isSystemTemplate()) {
            unset($validated['slug'], $validated['nome']);
        }

        $validated['variaveis_disponiveis'] = $this->normalizeVariables($validated['variaveis_disponiveis'] ?? []);
        $validated['ativo'] = $request->boolean('ativo');

        $emailTemplate->update($validated);

        return back()->with('admin_success', 'Template atualizado com sucesso.');
    }

    public function generateAi(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $validated = $request->validate([
            'descricao' => ['required', 'string', 'max:500'],
        ]);

        try {
            $generatedBody = GeminiService::gerarCorpoEmail($validated['descricao'], $emailTemplate->variaveis_disponiveis ?? []);
        } catch (\Throwable $exception) {
            return back()->with('admin_warning', $exception->getMessage());
        }

        $emailTemplate->update(['corpo' => $generatedBody]);

        return back()->with('admin_success', 'Corpo do e-mail gerado com IA e salvo no template.');
    }

    private function validateTemplate(Request $request, ?EmailTemplate $emailTemplate): array
    {
        return $request->validate([
            'slug' => [
                Rule::requiredIf($emailTemplate === null),
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('email_templates', 'slug')->ignore($emailTemplate?->id),
            ],
            'nome' => [Rule::requiredIf($emailTemplate === null), 'nullable', 'string', 'max:255'],
            'assunto' => ['required', 'string', 'max:255'],
            'saudacao' => ['nullable', 'string', 'max:255'],
            'corpo' => ['required', 'string'],
            'texto_acao' => ['nullable', 'string', 'max:255'],
            'url_acao' => ['nullable', 'string', 'max:255'],
            'texto_rodape' => ['nullable', 'string'],
            'variaveis_disponiveis' => ['nullable', 'array'],
            'variaveis_disponiveis.*' => ['string', 'max:255'],
        ]);
    }

    private function normalizeVariables(array $variables): array
    {
        return collect($variables)
            ->map(fn ($value) => trim((string) $value))
            ->filter()
            ->map(fn ($value) => str_replace(['{{', '}}', ' '], '', $value))
            ->unique()
            ->values()
            ->all();
    }
}