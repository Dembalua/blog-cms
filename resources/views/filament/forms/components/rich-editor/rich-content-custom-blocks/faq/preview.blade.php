<div class="text-xs space-y-1">
    @if($heading)
        <p class="font-semibold text-gray-200 mb-2">{{ $heading }}</p>
    @endif
    @forelse($items as $item)
        <div class="flex items-center justify-between gap-3 px-3 py-2 bg-primary-500/10 border border-primary-500/70 rounded-lg">
            <span class="text-gray-300 font-medium truncate">{{ Str::limit($item['question'] ?? '', 60) }}</span>
            <span class="text-primary-400/70 shrink-0 font-light" style="font-size: 1rem; line-height: 1;">+</span>
        </div>
    @empty
        <div class="px-3 py-2 text-gray-500 border border-dashed border-gray-700 rounded-lg text-center">
            No entries
        </div>
    @endforelse
</div>
