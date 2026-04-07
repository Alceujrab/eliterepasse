<?php

namespace App\Livewire;

use Livewire\Component;

class Vitrine extends Component
{
    public $searchTerm = '';
    public $category = '';
    public $brand = '';

    public function with(): array
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

        return [
            'vehicles' => $query->latest()->get()
        ];
    }

    public function render()
    {
        return view('livewire.vitrine');
    }
}
