<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LandingSettingsActionController extends Controller
{
    public function upsert(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'features' => ['nullable', 'array'],
            'features.*.title' => ['nullable', 'string', 'max:255'],
            'features.*.description' => ['nullable', 'string'],
            'features.*.icon' => ['nullable', 'string', 'max:80'],
            'faq' => ['nullable', 'array'],
            'faq.*.question' => ['nullable', 'string', 'max:255'],
            'faq.*.answer' => ['nullable', 'string'],
        ]);

        $features = collect($validated['features'] ?? [])
            ->map(fn (array $item) => [
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
                'icon' => trim((string) ($item['icon'] ?? '')),
            ])
            ->filter(fn (array $item) => $item['title'] !== '' || $item['description'] !== '' || $item['icon'] !== '')
            ->values()
            ->all();

        $faq = collect($validated['faq'] ?? [])
            ->map(fn (array $item) => [
                'question' => trim((string) ($item['question'] ?? '')),
                'answer' => trim((string) ($item['answer'] ?? '')),
            ])
            ->filter(fn (array $item) => $item['question'] !== '' || $item['answer'] !== '')
            ->values()
            ->all();

        $setting = LandingSetting::query()->latest('id')->first();

        $payload = [
            'hero_title' => $validated['hero_title'],
            'hero_subtitle' => $validated['hero_subtitle'],
            'whatsapp_number' => preg_replace('/\D/', '', $validated['whatsapp_number']),
            'features' => $features,
            'faq' => $faq,
        ];

        if ($setting) {
            $setting->update($payload);
        } else {
            LandingSetting::create($payload);
        }

        return redirect()->route('admin.v2.landing-settings.index')
            ->with('admin_success', 'Configuracoes da landing atualizadas com sucesso.');
    }
}