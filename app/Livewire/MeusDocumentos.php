<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MeusDocumentos extends Component
{
    public function render()
    {
        $documents = \App\Models\Document::with('vehicle')->where('user_id', auth()->id())->latest()->get();
        return view('livewire.meus-documentos', compact('documents'));
    }
}
