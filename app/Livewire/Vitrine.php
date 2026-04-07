<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Vitrine extends Component
{
    public $searchTerm = '';
    public $category = '';
    public $brand = '';

    public function render()
    {
        $query = \App\Models\Vehicle::query();

        if (!empty($this->searchTerm)) {
            $query->where('model', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('brand', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('plate', 'like', '%' . $this->searchTerm . '%');
        }

        if (!empty($this->category)) {
            $query->where('category', $this->category);
        }

        if (!empty($this->brand)) {
             $query->where('brand', $this->brand);
        }

        return view('livewire.vitrine', [
            'vehicles' => $query->latest()->get()
        ]);
    }
}
