@props(['items' => []])

@if(! empty($items))
    <nav aria-label="breadcrumbs" class="mb-3 text-xs font-semibold text-slate-500">
        <ol class="flex flex-wrap items-center gap-1.5">
            @foreach($items as $i => $item)
                @php($isLast = $i === array_key_last($items))
                <li class="flex items-center gap-1.5">
                    @if(! $isLast && ! empty($item['url']))
                        <a href="{{ $item['url'] }}" class="hover:text-blue-600 hover:underline">{{ $item['label'] }}</a>
                    @else
                        <span class="text-slate-700">{{ $item['label'] }}</span>
                    @endif
                    @unless($isLast)
                        <svg class="h-3 w-3 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
@endif
