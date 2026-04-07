<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Financeiro extends Component
{
    public function render()
    {
        $company = auth()->user()->companies()->first();
        
        // Busca os pedidos (faturas) que tem boletos ou que estao faturados
        $orders = $company ? \App\Models\Order::with(['vehicles', 'financial'])
            ->where('company_id', $company->id)
            ->whereIn('status', ['faturado', 'aguardando_pgto'])
            ->latest()
            ->get() : collect();

        return view('livewire.financeiro', [
            'orders' => $orders,
            'company' => $company
        ]);
    }
}
