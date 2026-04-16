<?php

namespace App\Livewire;

use App\Models\LandingSetting;
use App\Models\SystemSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.landing')]
class SobreNos extends Component
{
    public function render()
    {
        $settings = LandingSetting::first() ?? new LandingSetting(LandingSetting::defaults());
        $defaults = LandingSetting::defaults();

        $menuItems = collect($settings->menu_items ?? [])->filter(fn ($i) => filled($i['label'] ?? null))->values();
        if ($menuItems->isEmpty()) {
            $menuItems = collect($defaults['menu_items']);
        }
        $menuItems = $menuItems->map(function ($item) {
            if (($item['url'] ?? '') === '#contato') return array_merge($item, ['url' => '/contato']);
            if (($item['url'] ?? '') === '#sobre') return array_merge($item, ['url' => '/sobre-nos']);
            return $item;
        });

        $footerLinks = collect($settings->footer_links ?? [])->filter(fn ($i) => filled($i['label'] ?? null))->values();
        if ($footerLinks->isEmpty()) {
            $footerLinks = collect($defaults['footer_links']);
        }

        $logoUrl = $settings->logo_path
            ? asset($settings->logo_path)
            : asset('build/assets/logo.png');

        $stats = collect($settings->about_page_stats ?? [])->filter(fn ($s) => filled($s['value'] ?? null))->values();
        if ($stats->isEmpty()) {
            $stats = collect($defaults['about_page_stats']);
        }

        $team = collect($settings->about_page_team ?? [])->filter(fn ($t) => filled($t['name'] ?? null))->values();
        $testimonials = collect($settings->about_page_testimonials ?? [])->filter(fn ($t) => filled($t['name'] ?? null))->values();
        $gallery = collect($settings->about_page_gallery ?? [])->filter(fn ($g) => filled($g))->values();

        return view('livewire.sobre-nos', [
            'settings'     => $settings,
            'defaults'     => $defaults,
            'menuItems'    => $menuItems,
            'footerLinks'  => $footerLinks,
            'logoUrl'      => $logoUrl,
            'stats'        => $stats,
            'team'         => $team,
            'testimonials' => $testimonials,
            'gallery'      => $gallery,
        ]);
    }
}
