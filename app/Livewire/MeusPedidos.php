<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class MeusPedidos extends Component
{
    #[Layout('layouts.app')]
    public function render()
    {
        $company = auth()->user()->companies()->first();
        $orders = $company ? \App\Models\Order::with('vehicles')->where('company_id', $company->id)->latest()->get() : collect();

        return view('livewire.meus-pedidos', [
            'orders' => $orders,
            'company' => $company
        ]);
    }
}
