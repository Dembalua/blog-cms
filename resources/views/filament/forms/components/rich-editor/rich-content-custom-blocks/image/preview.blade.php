<div style="display: flex; justify-content: center;">
    @if($image)
        <img src="{{ $image->url }}" alt="{{ $image->alt ?? '' }}" style="max-width: 60%; height: auto; display: block; border-radius: 4px;">
    @else
        <div style="width: 60%; height: 100px; background: #e5e7eb; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
            <span style="color: #9ca3af; font-size: 0.7rem;">No image</span>
        </div>
    @endif
</div>
