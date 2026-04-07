<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class MeusPedidos extends Component
{
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
