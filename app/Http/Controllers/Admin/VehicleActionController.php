<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleActionController extends Controller
{
    public function updateStatus(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:available,reserved,sold'],
        ]);

        if ($vehicle->status === $validated['status']) {
            return back()->with('admin_warning', 'Este veiculo ja esta neste status.');
        }

        $vehicle->update(['status' => $validated['status']]);

        $label = Vehicle::statusLabels()[$validated['status']] ?? $validated['status'];

        return back()->with('admin_success', "Status do veiculo atualizado para {$label}.");
    }
}