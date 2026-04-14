<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\Attributes\Layout;
use App\Models\LandingSetting;

#[Layout('layouts.landing')]
class LandingPage extends Component
{
    public function render()
    {
        $settings = LandingSetting::first() ?? new LandingSetting(LandingSetting::defaults());

        return view('livewire.landing-page', [
            'settings' => $settings,
        ]);
    }
}
