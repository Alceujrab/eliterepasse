<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\Attributes\Layout;
use App\Models\LandingBanner;
use App\Models\LandingSetting;
use App\Models\SystemSetting;

#[Layout('layouts.landing')]
class LandingPage extends Component
{
    public function render()
    {
        $settings = LandingSetting::first() ?? new LandingSetting(LandingSetting::defaults());
        $banners = LandingBanner::active()->get();
        $mapsApiKey = SystemSetting::get('google_maps_api_key', '');

        return view('livewire.landing-page', [
            'settings' => $settings,
            'banners' => $banners,
            'mapsApiKey' => $mapsApiKey,
        ]);
    }
}
