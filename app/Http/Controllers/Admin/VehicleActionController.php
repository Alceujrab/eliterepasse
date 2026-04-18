<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VehicleActionController extends Controller
{
    private function rules(): array
    {
        return [
            'brand'                => ['required', 'string', 'max:60'],
            'model'                => ['required', 'string', 'max:80'],
            'version'              => ['required', 'string', 'max:120'],
            'plate'                => ['required', 'string', 'max:8'],
            'renavam'              => ['nullable', 'string', 'max:11'],
            'chassi'               => ['nullable', 'string', 'max:17'],
            'manufacture_year'     => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 1)],
            'model_year'           => ['required', 'integer', 'min:1990', 'max:' . (now()->year + 2)],
            'category'             => ['nullable', 'string', 'max:30'],
            'fipe_code'            => ['nullable', 'string', 'max:10'],
            'description'          => ['nullable', 'string', 'max:2000'],
            'mileage'              => ['required', 'integer', 'min:0'],
            'num_owners'           => ['nullable', 'integer', 'min:0', 'max:20'],
            'fuel_type'            => ['nullable', 'string', 'max:30'],
            'transmission'         => ['nullable', 'string', 'max:30'],
            'engine'               => ['nullable', 'string', 'max:60'],
            'steering'             => ['nullable', 'string', 'max:30'],
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
            'accepts_trade'        => ['nullable'],
            'ipva_paid'            => ['nullable'],
            'licensing_ok'         => ['nullable'],
            'is_armored'           => ['nullable'],
            'video_url'            => ['nullable', 'url', 'max:255'],
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
            'brand', 'model', 'version', 'plate', 'renavam', 'chassi',
            'manufacture_year', 'model_year', 'category', 'fipe_code',
            'description', 'mileage', 'num_owners', 'fuel_type', 'transmission',
            'engine', 'steering', 'color', 'doors', 'fipe_price', 'sale_price',
            'profit_margin', 'status', 'video_url',
        ]);

        $data['is_on_sale']           = $request->boolean('is_on_sale');
        $data['is_just_arrived']      = $request->boolean('is_just_arrived');
        $data['has_report']           = $request->boolean('has_report');
        $data['has_factory_warranty'] = $request->boolean('has_factory_warranty');
        $data['accepts_trade']        = $request->boolean('accepts_trade');
        $data['ipva_paid']            = $request->boolean('ipva_paid');
        $data['licensing_ok']         = $request->boolean('licensing_ok');
        $data['is_armored']           = $request->boolean('is_armored');

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
            foreach ($remove as $url) {
                // Extrai o caminho relativo da URL para deletar o arquivo físico
                $parsed = parse_url($url, PHP_URL_PATH);
                if ($parsed) {
                    $fullPath = public_path(ltrim($parsed, '/'));
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
            $existing = array_values(array_diff($existing, $remove));
        }

        // Novas fotos (salva direto em public/uploads/vehicles para evitar symlink)
        if ($request->hasFile('photos')) {
            $dir = public_path('uploads/vehicles');
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            foreach ($request->file('photos') as $photo) {
                $name = uniqid('v_') . '_' . time() . '.' . $photo->getClientOriginalExtension();
                $photo->move($dir, $name);
                $existing[] = asset('uploads/vehicles/' . $name);
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