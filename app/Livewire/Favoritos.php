<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Favorite;

#[Layout('layouts.app')]
class Favoritos extends Component
{
    public function removeFavorite($id)
    {
        Favorite::where('id', $id)->delete();
    }

    public function render()
    {
        $favorites = Favorite::with('vehicle')->where('user_id', auth()->id())->latest()->get();
        return view('livewire.favoritos', compact('favorites'));
    }
}
