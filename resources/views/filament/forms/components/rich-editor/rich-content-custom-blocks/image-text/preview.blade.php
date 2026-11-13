<div style="display: flex; gap: 1rem; align-items: flex-start;">
    @if($imagePosition === 'right')
        <div style="flex: 1; min-width: 0;">
            @if($heading)
                <strong style="display: block; margin-bottom: 0.25rem; font-size: 0.9rem;">{{ $heading }}</strong>
            @endif
            <p style="margin: 0; font-size: 0.8rem; color: #6b7280; line-height: 1.4;">{{ Str::limit($body, 120) }}</p>
        </div>
        <div style="flex: 0 0 38%; max-width: 38%;">
            @if($image)
                <img src="{{ $image->url }}" alt="" style="width: 100%; height: auto; display: block; border-radius: 4px;">
            @else
                <div style="width: 100%; height: 80px; background: #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: #9ca3af; font-size: 0.7rem;">No image</span>
                </div>
            @endif
        </div>
    @else
        <div style="flex: 0 0 38%; max-width: 38%;">
            @if($image)
                <img src="{{ $image->url }}" alt="" style="width: 100%; height: auto; display: block; border-radius: 4px;">
            @else
                <div style="width: 100%; height: 80px; background: #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                    <span style="color: #9ca3af; font-size: 0.7rem;">No image</span>
                </div>
            @endif
        </div>
        <div style="flex: 1; min-width: 0;">
            @if($heading)
                <strong style="display: block; margin-bottom: 0.25rem; font-size: 0.9rem;">{{ $heading }}</strong>
            @endif
            <p style="margin: 0; font-size: 0.8rem; color: #6b7280; line-height: 1.4;">{{ Str::limit($body, 120) }}</p>
        </div>
    @endif
</div>
