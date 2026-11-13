<div class="text-xs space-y-1">
    @if($heading)
        <p class="font-semibold text-gray-200 mb-2">{{ $heading }}</p>
    @endif
    @forelse($items as $item)
        <div class="flex items-center gap-3 px-3 py-2 bg-primary-500/10 border-l-2 border-primary-500/70 rounded-r-lg">
            <span class="text-gray-300 font-medium truncate">{{ Str::limit($item['heading'] ?? '', 60) }}</span>
        </div>
    @empty
        <div class="px-3 py-2 text-gray-500 border border-dashed border-gray-700 rounded-lg text-center">
            No sections
        </div>
    @endforelse
</div>
