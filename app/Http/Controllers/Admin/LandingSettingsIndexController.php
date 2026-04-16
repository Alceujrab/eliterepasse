<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingBanner;
use App\Models\LandingSetting;
use App\Models\SystemSetting;
use Illuminate\View\View;

class LandingSettingsIndexController extends Controller
{
    public function __invoke(): View
    {
        $setting = LandingSetting::query()->latest('id')->first() ?? new LandingSetting(LandingSetting::defaults());
        $defaults = LandingSetting::defaults();

        $features = collect(old('features', $setting->features ?? []))->filter(fn ($i) => filled($i['title'] ?? null))->values()->all();
        if (empty($features)) { $features = $defaults['features']; }

        $faq = collect(old('faq', $setting->faq ?? []))->filter(fn ($i) => filled($i['question'] ?? null))->values()->all();
        if (empty($faq)) { $faq = $defaults['faq']; }

        $menuItems = collect(old('menu_items', $setting->menu_items ?? []))->filter(fn ($i) => filled($i['label'] ?? null))->values()->all();
        if (empty($menuItems)) { $menuItems = $defaults['menu_items']; }

        $footerLinks = collect(old('footer_links', $setting->footer_links ?? []))->filter(fn ($i) => filled($i['label'] ?? null))->values()->all();
        if (empty($footerLinks)) { $footerLinks = $defaults['footer_links']; }

        while (count($features) < 6) {
            $features[] = ['title' => '', 'description' => '', 'icon' => ''];
        }
        while (count($faq) < 6) {
            $faq[] = ['question' => '', 'answer' => ''];
        }
        while (count($menuItems) < 8) {
            $menuItems[] = ['label' => '', 'url' => ''];
        }
        while (count($footerLinks) < 6) {
            $footerLinks[] = ['label' => '', 'url' => ''];
        }

        $banners = LandingBanner::query()->orderBy('order')->get();
        $mapsApiKey = SystemSetting::where('key', 'google_maps_api_key')->value('value');

        return view('admin.landing-settings.index', [
            'setting' => $setting,
            'featuresRows' => $features,
            'faqRows' => $faq,
            'menuItemsRows' => $menuItems,
            'footerLinksRows' => $footerLinks,
            'banners' => $banners,
            'mapsApiKey' => $mapsApiKey,
        ]);
    }
}