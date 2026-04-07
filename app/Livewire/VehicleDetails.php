<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Vehicle;
use App\Models\Favorite;

#[Layout('layouts.app')]
class VehicleDetails extends Component
{
    public $vehicle;
    public $isFavorited = false;
    
    public function mount($id)
    {
        $this->vehicle = Vehicle::findOrFail($id);
        $this->checkFavorite();
    }

    public function checkFavorite()
    {
        if (auth()->check()) {
            $this->isFavorited = Favorite::where('user_id', auth()->id())
                ->where('vehicle_id', $this->vehicle->id)
                ->exists();
        }
    }

    public function toggleFavorite()
    {
        if (!auth()->check()) return redirect()->route('login');

        if ($this->isFavorited) {
            Favorite::where('user_id', auth()->id())->where('vehicle_id', $this->vehicle->id)->delete();
            $this->isFavorited = false;
        } else {
            Favorite::create([
                'user_id' => auth()->id(),
                'vehicle_id' => $this->vehicle->id
            ]);
            $this->isFavorited = true;
        }
    }

    public function render()
    {
        return view('livewire.vehicle-details');
    }
}
