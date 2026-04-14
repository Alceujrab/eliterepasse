<div class="flex flex-wrap gap-2">
    @if($vehicle->status !== 'available')
        <form method="POST" action="{{ route('admin.v2.vehicles.status', $vehicle) }}">
            @csrf
            <input type="hidden" name="status" value="available">
            <button type="submit" class="admin-btn-primary">Disponibilizar</button>
        </form>
    @endif

    @if($vehicle->status !== 'reserved')
        <form method="POST" action="{{ route('admin.v2.vehicles.status', $vehicle) }}">
            @csrf
            <input type="hidden" name="status" value="reserved">
            <button type="submit" class="admin-btn-soft">Reservar</button>
        </form>
    @endif

    @if($vehicle->status !== 'sold')
        <form method="POST" action="{{ route('admin.v2.vehicles.status', $vehicle) }}">
            @csrf
            <input type="hidden" name="status" value="sold">
            <button type="submit" class="admin-btn-soft">Marcar vendido</button>
        </form>
    @endif

    <a href="{{ route('admin.v2.vehicles.show', $vehicle) }}" class="admin-btn-soft">Abrir v2</a>
</div>