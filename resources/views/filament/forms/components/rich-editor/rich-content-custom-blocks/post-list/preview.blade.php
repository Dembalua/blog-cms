<div class="text-xs space-y-2">
    @if($heading)
        <p class="font-semibold text-gray-200">{{ $heading }}</p>
    @endif
    @if($intro)
        <p class="text-gray-400">{{ Str::limit($intro, 120) }}</p>
    @endif
    <div class="flex items-center gap-2 px-3 py-2 bg-primary-500/10 border-l-2 border-primary-500/70 rounded-r-lg">
        <span class="text-gray-300 font-medium">{{ count($items) }} / {{ $count }}</span>
        <span class="text-gray-500">·</span>
        <span class="text-gray-400">{{ __('rich-editor-blocks.post_list.preview.count_label') }}</span>
    </div>
</div>
