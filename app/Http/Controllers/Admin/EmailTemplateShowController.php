<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\View\View;

class EmailTemplateShowController extends Controller
{
    public function __invoke(EmailTemplate $emailTemplate): View
    {
        $sampleVariables = collect($emailTemplate->variaveis_disponiveis ?? [])
            ->mapWithKeys(fn (string $variable) => [$variable => strtoupper($variable)])
            ->all();

        return view('admin.email-templates.show', [
            'emailTemplate' => $emailTemplate,
            'sampleVariables' => $sampleVariables,
            'preview' => [
                'assunto' => EmailTemplate::renderText($emailTemplate->assunto, $sampleVariables),
                'saudacao' => EmailTemplate::renderText($emailTemplate->saudacao, $sampleVariables),
                'corpo' => EmailTemplate::renderText($emailTemplate->corpo, $sampleVariables),
                'texto_acao' => EmailTemplate::renderText($emailTemplate->texto_acao, $sampleVariables),
                'url_acao' => EmailTemplate::renderText($emailTemplate->url_acao, $sampleVariables),
                'texto_rodape' => EmailTemplate::renderText($emailTemplate->texto_rodape, $sampleVariables),
            ],
        ]);
    }
}