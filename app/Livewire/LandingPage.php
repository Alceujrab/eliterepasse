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
        return view('livewire.landing-page', [
            'settings' => LandingSetting::first() ?? new LandingSetting()
        ]);
    }
}
