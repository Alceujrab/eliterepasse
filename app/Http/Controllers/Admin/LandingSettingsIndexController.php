<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingSetting;
use Illuminate\View\View;

class LandingSettingsIndexController extends Controller
{
    public function __invoke(): View
    {
        $setting = LandingSetting::query()->latest('id')->first() ?? new LandingSetting(LandingSetting::defaults());

        $features = collect(old('features', $setting->features ?? []))->take(6)->values()->all();
        $faq = collect(old('faq', $setting->faq ?? []))->take(6)->values()->all();

        while (count($features) < 6) {
            $features[] = ['title' => '', 'description' => '', 'icon' => ''];
        }

        while (count($faq) < 6) {
            $faq[] = ['question' => '', 'answer' => ''];
        }

        return view('admin.landing-settings.index', [
            'setting' => $setting,
            'featuresRows' => $features,
            'faqRows' => $faq,
        ]);
    }
}