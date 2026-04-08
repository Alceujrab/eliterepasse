<?php

namespace App\Livewire;

use App\Models\Favorite;
use App\Models\Vehicle;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Vitrine extends Component
{
    use WithPagination;

    // ─── Filtros via URL ──────────────────────────────────────────────
    #[Url(except: '')]
    public string $searchTerm = '';

    #[Url(except: [])]
    public array $brands = [];

    #[Url(except: [])]
    public array $categories = [];

    #[Url(except: [])]
    public array $fuelTypes = [];

    #[Url(except: [])]
    public array $transmissions = [];

    #[Url(except: '')]
    public string $priceMin = '';

    #[Url(except: '')]
    public string $priceMax = '';

    #[Url(except: '')]
    public string $yearMin = '';

    #[Url(except: false)]
    public bool $vehiclesOnSale = false;

    #[Url(except: false)]
    public bool $carsWithReport = false;

    #[Url(except: false)]
    public bool $factoryWarranty = false;

    #[Url(except: false)]
    public bool $justArrived = false;

    #[Url(except: 'recentes')]
    public string $ordenar = 'recentes';

    // ─── UI State ────────────────────────────────────────────────────
    public bool $showFilters = false;
    public string $viewMode  = 'grid'; // grid | list

    // ─── Reset page on filter change ─────────────────────────────────
    public function updatedSearchTerm()  { $this->resetPage(); }
    public function updatedBrands()      { $this->resetPage(); }
    public function updatedCategories()  { $this->resetPage(); }
    public function updatedFuelTypes()   { $this->resetPage(); }
    public function updatedTransmissions(){ $this->resetPage(); }
    public function updatedPriceMin()    { $this->resetPage(); }
    public function updatedPriceMax()    { $this->resetPage(); }
    public function updatedOrdenar()     { $this->resetPage(); }
    public function updatedVehiclesOnSale(){ $this->resetPage(); }
    public function updatedCarsWithReport(){ $this->resetPage(); }
    public function updatedFactoryWarranty(){ $this->resetPage(); }
    public function updatedJustArrived()  { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['searchTerm', 'brands', 'categories', 'fuelTypes',
            'transmissions', 'priceMin', 'priceMax', 'yearMin',
            'vehiclesOnSale', 'carsWithReport', 'factoryWarranty', 'justArrived']);
        $this->ordenar = 'recentes';
        $this->resetPage();
    }

    public function toggleFavorite(int $vehicleId): void
    {
        if (! auth()->check()) {
            redirect()->route('login');
            return;
        }
        $exists = Favorite::where('user_id', auth()->id())->where('vehicle_id', $vehicleId)->exists();
        $exists
            ? Favorite::where('user_id', auth()->id())->where('vehicle_id', $vehicleId)->delete()
            : Favorite::create(['user_id' => auth()->id(), 'vehicle_id' => $vehicleId]);
    }

    public function getActiveFiltersCountProperty(): int
    {
        return count(array_filter([
            $this->searchTerm, $this->brands, $this->categories, $this->fuelTypes,
            $this->transmissions, $this->priceMin, $this->priceMax, $this->yearMin,
            $this->vehiclesOnSale, $this->carsWithReport, $this->factoryWarranty, $this->justArrived,
        ]));
    }

    public function render()
    {
        $query = Vehicle::query()->where('status', 'available');

        // Busca textual
        if ($this->searchTerm) {
            $query->where(fn ($q) =>
                $q->where('model', 'LIKE', "%{$this->searchTerm}%")
                  ->orWhere('brand', 'LIKE', "%{$this->searchTerm}%")
                  ->orWhere('plate', 'LIKE', "%{$this->searchTerm}%")
                  ->orWhere('version', 'LIKE', "%{$this->searchTerm}%")
            );
        }

        // Filtros multi-select
        if ($this->brands)       $query->whereIn('brand', $this->brands);
        if ($this->categories)   $query->whereIn('category', $this->categories);
        if ($this->fuelTypes)    $query->whereIn('fuel_type', $this->fuelTypes);
        if ($this->transmissions) $query->whereIn('transmission', $this->transmissions);

        // Faixa de preço
        if ($this->priceMin) $query->where('sale_price', '>=', (float) str_replace(['.', ','], ['', '.'], $this->priceMin));
        if ($this->priceMax) $query->where('sale_price', '<=', (float) str_replace(['.', ','], ['', '.'], $this->priceMax));

        // Ano mínimo
        if ($this->yearMin) $query->where('model_year', '>=', (int) $this->yearMin);

        // Switches
        if ($this->vehiclesOnSale)  $query->where('is_on_sale', true);
        if ($this->carsWithReport)  $query->where('has_report', true);
        if ($this->factoryWarranty) $query->where('has_factory_warranty', true);
        if ($this->justArrived)     $query->where('is_just_arrived', true);

        // Ordenação
        $query = match($this->ordenar) {
            'preco_asc'  => $query->orderBy('sale_price'),
            'preco_desc' => $query->orderByDesc('sale_price'),
            'km_asc'     => $query->orderBy('mileage'),
            'ano_desc'   => $query->orderByDesc('model_year'),
            default      => $query->latest(),
        };

        $vehicles       = $query->paginate(18);
        $totalSemFiltro = Vehicle::where('status', 'available')->count();

        $userFavorites = auth()->check()
            ? Favorite::where('user_id', auth()->id())->pluck('vehicle_id')->toArray()
            : [];

        // Opções dinâmicas (baseadas no BD real)
        $availableBrands     = Vehicle::where('status', 'available')->distinct()->orderBy('brand')->pluck('brand')->filter()->values();
        $availableCategories = Vehicle::where('status', 'available')->distinct()->orderBy('category')->pluck('category')->filter()->values();
        $availableFuelTypes  = Vehicle::where('status', 'available')->distinct()->orderBy('fuel_type')->pluck('fuel_type')->filter()->values();
        $availableTransmissions = Vehicle::where('status', 'available')->distinct()->orderBy('transmission')->pluck('transmission')->filter()->values();

        return view('livewire.vitrine', compact(
            'vehicles', 'userFavorites', 'totalSemFiltro',
            'availableBrands', 'availableCategories', 'availableFuelTypes', 'availableTransmissions'
        ));
    }
}

