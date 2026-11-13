<div class="text-xs space-y-2">
    @if($heading)
        <p class="font-semibold text-gray-200">{{ $heading }}</p>
    @endif

    @if(!empty($questions))
        <div class="space-y-1">
            <p class="text-gray-400 uppercase tracking-wide text-[10px]">Questions</p>
            @foreach($questions as $i => $question)
                <div class="flex items-center gap-2 px-3 py-1.5 bg-primary-500/10 border-l-2 border-primary-500/70 rounded-r-lg">
                    <span class="text-gray-500">{{ $i + 1 }}.</span>
                    <span class="text-gray-300 font-medium truncate">{{ Str::limit($question['question'] ?? '', 60) }}</span>
                    <span class="ml-auto text-gray-500 text-[10px]">{{ count($question['answers'] ?? []) }} answers</span>
                </div>
            @endforeach
        </div>
    @endif

    @if(!empty($results))
        <div class="space-y-1">
            <p class="text-gray-400 uppercase tracking-wide text-[10px]">Result ranges</p>
            @foreach($results as $result)
                <div class="flex items-center gap-2 px-3 py-1.5 bg-gray-500/10 border-l-2 border-gray-500/70 rounded-r-lg">
                    <span class="text-gray-500 text-[10px]">{{ $result['min_points'] ?? '?' }}&ndash;{{ $result['max_points'] ?? '?' }}</span>
                    <span class="text-gray-300 font-medium truncate">{{ Str::limit($result['heading'] ?? '', 50) }}</span>
                </div>
            @endforeach
        </div>
    @endif

    @if(empty($questions) && empty($results))
        <div class="px-3 py-2 text-gray-500 border border-dashed border-gray-700 rounded-lg text-center">
            No quiz content yet
        </div>
    @endif
</div>
