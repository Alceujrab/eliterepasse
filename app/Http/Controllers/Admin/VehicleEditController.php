<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\VehicleCreateController;
use App\Models\Vehicle;
use Illuminate\View\View;

class VehicleEditController extends Controller
{
    public function __invoke(Vehicle $vehicle): View
    {
        return view('admin.vehicles.edit', [
            'vehicle' => $vehicle,
        ] + VehicleCreateController::formOptions());
    }
}
