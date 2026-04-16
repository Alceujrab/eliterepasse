<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LandingSettingsActionController extends Controller
{
    public function upsert(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:255'],
            'hero_subtitle' => ['required', 'string', 'max:255'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'menu_items' => ['nullable', 'array'],
            'menu_items.*.label' => ['nullable', 'string', 'max:255'],
            'menu_items.*.url' => ['nullable', 'string', 'max:255'],
            'features' => ['nullable', 'array'],
            'features.*.title' => ['nullable', 'string', 'max:255'],
            'features.*.description' => ['nullable', 'string'],
            'features.*.icon' => ['nullable', 'string', 'max:80'],
            'faq' => ['nullable', 'array'],
            'faq.*.question' => ['nullable', 'string', 'max:255'],
            'faq.*.answer' => ['nullable', 'string'],
            'about_title' => ['nullable', 'string', 'max:255'],
            'about_text' => ['nullable', 'string'],
            'about_image' => ['nullable', 'image', 'max:2048'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_city' => ['nullable', 'string', 'max:100'],
            'contact_state' => ['nullable', 'string', 'max:2'],
            'contact_lat' => ['nullable', 'string', 'max:50'],
            'contact_lng' => ['nullable', 'string', 'max:50'],
            'footer_text' => ['nullable', 'string', 'max:500'],
            'footer_links' => ['nullable', 'array'],
            'footer_links.*.label' => ['nullable', 'string', 'max:255'],
            'footer_links.*.url' => ['nullable', 'string', 'max:255'],
            'social_instagram' => ['nullable', 'string', 'max:255'],
            'social_facebook' => ['nullable', 'string', 'max:255'],
            'social_youtube' => ['nullable', 'string', 'max:255'],
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

        $menuItems = collect($validated['menu_items'] ?? [])
            ->map(fn (array $item) => [
                'label' => trim((string) ($item['label'] ?? '')),
                'url' => trim((string) ($item['url'] ?? '')),
            ])
            ->filter(fn (array $item) => $item['label'] !== '' || $item['url'] !== '')
            ->values()
            ->all();

        $footerLinks = collect($validated['footer_links'] ?? [])
            ->map(fn (array $item) => [
                'label' => trim((string) ($item['label'] ?? '')),
                'url' => trim((string) ($item['url'] ?? '')),
            ])
            ->filter(fn (array $item) => $item['label'] !== '' || $item['url'] !== '')
            ->values()
            ->all();

        $setting = LandingSetting::query()->latest('id')->first();

        $payload = [
            'hero_title' => $validated['hero_title'],
            'hero_subtitle' => $validated['hero_subtitle'],
            'whatsapp_number' => preg_replace('/\D/', '', $validated['whatsapp_number']),
            'menu_items' => $menuItems,
            'features' => $features,
            'faq' => $faq,
            'about_title' => $validated['about_title'] ?? null,
            'about_text' => $validated['about_text'] ?? null,
            'contact_phone' => $validated['contact_phone'] ?? null,
            'contact_email' => $validated['contact_email'] ?? null,
            'contact_address' => $validated['contact_address'] ?? null,
            'contact_city' => $validated['contact_city'] ?? null,
            'contact_state' => $validated['contact_state'] ?? null,
            'contact_lat' => $validated['contact_lat'] ?? null,
            'contact_lng' => $validated['contact_lng'] ?? null,
            'footer_text' => $validated['footer_text'] ?? null,
            'footer_links' => $footerLinks,
            'social_instagram' => $validated['social_instagram'] ?? null,
            'social_facebook' => $validated['social_facebook'] ?? null,
            'social_youtube' => $validated['social_youtube'] ?? null,
        ];

        // Upload de logo
        if ($request->hasFile('logo')) {
            if ($setting && $setting->logo_path) {
                Storage::disk('public')->delete($setting->logo_path);
            }
            $payload['logo_path'] = $request->file('logo')->store('landing', 'public');
        }

        // Upload de imagem "Sobre Nós"
        if ($request->hasFile('about_image')) {
            if ($setting && $setting->about_image) {
                Storage::disk('public')->delete($setting->about_image);
            }
            $payload['about_image'] = $request->file('about_image')->store('landing', 'public');
        }

        if ($setting) {
            $setting->update($payload);
        } else {
            LandingSetting::create($payload);
        }

        return redirect()->route('admin.v2.landing-settings.index')
            ->with('admin_success', 'Configuracoes da landing atualizadas com sucesso.');
    }
}