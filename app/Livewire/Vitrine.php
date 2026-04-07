<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Vehicle;
use App\Models\Favorite;

#[Layout('layouts.app')]
class Vitrine extends Component
{
    public $searchTerm = '';
    public $categories = [];
    public $brands = [];

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'categories' => ['except' => []],
        'brands' => ['except' => []],
    ];

    public function toggleFavorite($vehicleId)
    {
        if (!auth()->check()) return redirect()->route('login');

        $exists = Favorite::where('user_id', auth()->id())->where('vehicle_id', $vehicleId)->exists();
        if ($exists) {
            Favorite::where('user_id', auth()->id())->where('vehicle_id', $vehicleId)->delete();
        } else {
            Favorite::create(['user_id' => auth()->id(), 'vehicle_id' => $vehicleId]);
        }
    }

    public function removeFilter($type, $value)
    {
        if ($type === 'brands') {
            $this->brands = array_diff($this->brands, [$value]);
        }
        if ($type === 'categories') {
            $this->categories = array_diff($this->categories, [$value]);
        }
    }

    public function render()
    {
        $query = Vehicle::query();

        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('model', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('plate', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if (!empty($this->categories)) {
            $query->whereIn('category', $this->categories);
        }

        if (!empty($this->brands)) {
             $query->whereIn('brand', $this->brands);
        }

        $vehicles = $query->latest()->get();
        
        $userFavorites = [];
        if (auth()->check()) {
            $userFavorites = Favorite::where('user_id', auth()->id())->pluck('vehicle_id')->toArray();
        }

        // Available options for checkboxes based on current DB (optional dynamic list, but fixed for now)
        $availableBrands = ['Volkswagen', 'Jeep', 'Chevrolet', 'Fiat', 'Toyota', 'Hyundai', 'Honda'];
        $availableCategories = ['Hatch', 'Sedan', 'SUV', 'Picape'];

        return view('livewire.vitrine', compact('vehicles', 'userFavorites', 'availableBrands', 'availableCategories'));
    }
}
