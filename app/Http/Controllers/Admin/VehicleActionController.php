<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehicleActionController extends Controller
{
    private function rules(): array
    {
        return [
            'brand'                => ['required', 'string', 'max:60'],
            'model'                => ['required', 'string', 'max:80'],
            'version'              => ['required', 'string', 'max:120'],
            'plate'                => ['required', 'string', 'max:8'],
            'manufacture_year'     => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 1)],
            'model_year'           => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 2)],
            'category'             => ['nullable', 'string', 'max:30'],
            'fipe_code'            => ['nullable', 'string', 'max:10'],
            'mileage'              => ['required', 'integer', 'min:0'],
            'fuel_type'            => ['nullable', 'string', 'max:30'],
            'transmission'         => ['nullable', 'string', 'max:30'],
            'engine'               => ['nullable', 'string', 'max:60'],
            'color'                => ['nullable', 'string', 'max:40'],
            'doors'                => ['nullable', 'integer', 'min:2', 'max:5'],
            'fipe_price'           => ['nullable', 'numeric', 'min:0'],
            'sale_price'           => ['nullable', 'numeric', 'min:0'],
            'profit_margin'        => ['nullable', 'numeric'],
            'status'               => ['required', 'in:available,reserved,sold'],
            'is_on_sale'           => ['nullable'],
            'is_just_arrived'      => ['nullable'],
            'has_report'           => ['nullable'],
            'has_factory_warranty' => ['nullable'],
            'location_name'        => ['nullable', 'string', 'max:80'],
            'location_city'        => ['nullable', 'string', 'max:80'],
            'location_state'       => ['nullable', 'string', 'max:2'],
            'accessories'          => ['nullable', 'string'],
            'photos.*'             => ['image', 'max:5120'],
        ];
    }

    private function buildData(Request $request): array
    {
        $data = $request->only([
            'brand', 'model', 'version', 'plate', 'manufacture_year', 'model_year',
            'category', 'fipe_code', 'mileage', 'fuel_type', 'transmission', 'engine',
            'color', 'doors', 'fipe_price', 'sale_price', 'profit_margin', 'status',
        ]);

        $data['is_on_sale']           = $request->boolean('is_on_sale');
        $data['is_just_arrived']      = $request->boolean('is_just_arrived');
        $data['has_report']           = $request->boolean('has_report');
        $data['has_factory_warranty'] = $request->boolean('has_factory_warranty');

        $data['location'] = [
            'name'  => $request->input('location_name'),
            'city'  => $request->input('location_city'),
            'state' => $request->input('location_state'),
        ];

        $accessories = $request->input('accessories');
        $data['accessories'] = $accessories
            ? array_map('trim', explode(',', $accessories))
            : [];

        return $data;
    }

    private function handlePhotos(Request $request, Vehicle $vehicle): void
    {
        $existing = $vehicle->media ?? [];

        // Remove fotos marcadas
        $remove = $request->input('remove_photos', []);
        if (! empty($remove)) {
            foreach ($remove as $path) {
                Storage::disk('public')->delete($path);
            }
            $existing = array_values(array_diff($existing, $remove));
        }

        // Novas fotos
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $existing[] = $photo->store('vehicles', 'public');
            }
        }

        $vehicle->update(['media' => array_values($existing)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate($this->rules());

        $vehicle = Vehicle::create($this->buildData($request));

        $this->handlePhotos($request, $vehicle);

        return redirect()
            ->route('admin.v2.vehicles.show', $vehicle)
            ->with('admin_success', 'Veiculo cadastrado com sucesso.');
    }

    public function update(Request $request, Vehicle $vehicle): RedirectResponse
    {
        $request->validate($this->rules());

        $vehicle->update($this->buildData($request));

        $this->handlePhotos($request, $vehicle);

        return redirect()
            ->route('admin.v2.vehicles.show', $vehicle)
            ->with('admin_success', 'Veiculo atualizado com sucesso.');
    }

    public function destroy(Vehicle $vehicle): RedirectResponse
    {
        if ($vehicle->orders()->exists()) {
            return back()->with('admin_warning', 'Nao e possivel excluir um veiculo com pedidos vinculados.');
        }

        // Remove fotos
        foreach ($vehicle->media ?? [] as $path) {
            Storage::disk('public')->delete($path);
        }

        $vehicle->delete();

        return redirect()
            ->route('admin.v2.vehicles.index')
            ->with('admin_success', 'Veiculo excluido com sucesso.');
    }

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