<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class DocumentosElite extends Component
{
    public function render()
    {
        $documents = \App\Models\GeneralDocument::where('is_active', true)->latest()->get();
        return view('livewire.documentos-elite', compact('documents'));
    }
}
