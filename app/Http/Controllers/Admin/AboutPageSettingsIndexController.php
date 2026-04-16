<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\View\View;

class AboutPageSettingsIndexController extends Controller
{
    public function __invoke(): View
    {
        $setting = LandingSetting::query()->latest('id')->first() ?? new LandingSetting(LandingSetting::defaults());
        $defaults = LandingSetting::defaults();

        $stats = collect(old('about_page_stats', $setting->about_page_stats ?? []))
            ->filter(fn ($s) => filled($s['value'] ?? null))->values()->all();
        if (empty($stats)) {
            $stats = $defaults['about_page_stats'];
        }
        while (count($stats) < 6) {
            $stats[] = ['value' => '', 'label' => ''];
        }

        $team = collect(old('about_page_team', $setting->about_page_team ?? []))
            ->filter(fn ($t) => filled($t['name'] ?? null))->values()->all();
        while (count($team) < 6) {
            $team[] = ['name' => '', 'role' => '', 'photo' => '', 'bio' => ''];
        }

        $testimonials = collect(old('about_page_testimonials', $setting->about_page_testimonials ?? []))
            ->filter(fn ($t) => filled($t['name'] ?? null))->values()->all();
        while (count($testimonials) < 6) {
            $testimonials[] = ['name' => '', 'role' => '', 'company' => '', 'text' => '', 'photo' => '', 'video_url' => '', 'rating' => '5'];
        }

        $gallery = collect($setting->about_page_gallery ?? [])->filter(fn ($g) => filled($g))->values()->all();

        return view('admin.about-page.index', [
            'setting'      => $setting,
            'defaults'     => $defaults,
            'statsRows'    => $stats,
            'teamRows'     => $team,
            'testimonialRows' => $testimonials,
            'gallery'      => $gallery,
        ]);
    }
}
