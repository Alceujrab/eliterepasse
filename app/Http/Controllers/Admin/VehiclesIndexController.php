<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VehiclesIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $status = $request->string('status')->toString();
        $brand = $request->string('brand')->toString();
        $highlight = $request->string('highlight')->toString();
        $search = trim($request->string('q')->toString());

        $queryFactory = function () use ($status, $brand, $highlight, $search): Builder {
            return Vehicle::query()
                ->withCount(['orders', 'documents', 'reports'])
                ->when($status !== '', fn (Builder $query) => $query->where('status', $status))
                ->when($brand !== '', fn (Builder $query) => $query->where('brand', $brand))
                ->when($highlight !== '', function (Builder $query) use ($highlight) {
                    return match ($highlight) {
                        'offer' => $query->where('is_on_sale', true),
                        'report' => $query->where('has_report', true),
                        'warranty' => $query->where('has_factory_warranty', true),
                        'arrived' => $query->where('is_just_arrived', true),
                        default => $query,
                    };
                })
                ->when($search !== '', function (Builder $query) use ($search) {
                    $query->where(function (Builder $subQuery) use ($search) {
                        $subQuery
                            ->where('plate', 'like', "%{$search}%")
                            ->orWhere('brand', 'like', "%{$search}%")
                            ->orWhere('model', 'like', "%{$search}%")
                            ->orWhere('version', 'like', "%{$search}%")
                            ->orWhere('color', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%")
                            ->orWhere('fipe_code', 'like', "%{$search}%");
                    });
                });
        };

        $vehicles = $queryFactory()
            ->orderByRaw("FIELD(status, 'available', 'reserved', 'sold')")
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        $summary = [
            'filteredTotal' => $vehicles->total(),
            'available' => $queryFactory()->where('status', 'available')->count(),
            'reserved' => $queryFactory()->where('status', 'reserved')->count(),
            'sold' => $queryFactory()->where('status', 'sold')->count(),
            'stockValue' => (float) $queryFactory()->where('status', 'available')->sum('sale_price'),
            'onSale' => $queryFactory()->where('is_on_sale', true)->count(),
        ];

        return view('admin.vehicles.index', [
            'vehicles' => $vehicles,
            'status' => $status,
            'brand' => $brand,
            'highlight' => $highlight,
            'search' => $search,
            'summary' => $summary,
            'globalTotalVehicles' => Vehicle::count(),
            'hasActiveFilters' => $status !== '' || $brand !== '' || $highlight !== '' || $search !== '',
            'statusOptions' => Vehicle::statusLabels(),
            'highlightOptions' => [
                'offer' => 'Em oferta',
                'report' => 'Com laudo',
                'warranty' => 'Com garantia',
                'arrived' => 'Recem chegado',
            ],
            'brandOptions' => Vehicle::query()->whereNotNull('brand')->orderBy('brand')->distinct()->pluck('brand', 'brand'),
        ]);
    }
}