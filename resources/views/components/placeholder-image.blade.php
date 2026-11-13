@props(['wide' => false])

<div {{ $attributes->merge(['class' => 'relative overflow-hidden']) }}
     style="background: linear-gradient(135deg, color-mix(in srgb, var(--color-primary) 35%, var(--color-background)) 0%, var(--color-background) 55%, color-mix(in srgb, var(--color-secondary) 16%, var(--color-background)) 100%)">

    @if($wide)
        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 1200 300" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <ellipse cx="1000" cy="120" rx="380" ry="240" fill="var(--color-primary)" opacity="0.07"/>
            <path d="M -60 400 Q 400 -60 1260 160" stroke="var(--color-primary)" stroke-width="1"    opacity="0.35"/>
            <path d="M -60 460 Q 400  -0 1260 220" stroke="var(--color-primary)" stroke-width="0.75" opacity="0.25"/>
            <path d="M -60 520 Q 400  60 1260 280" stroke="var(--color-primary)" stroke-width="0.5"  opacity="0.18"/>
            <path d="M -60 340 Q 380 -120 1260 100" stroke="var(--color-primary)" stroke-width="0.5"  opacity="0.14"/>
        </svg>
    @else
        <svg class="absolute inset-0 w-full h-full" viewBox="0 0 400 200" fill="none" preserveAspectRatio="xMidYMid slice" aria-hidden="true">
            <ellipse cx="340" cy="60" rx="200" ry="150" fill="var(--color-primary)" opacity="0.08"/>
            <path d="M -20 250 Q 180 -40 460 90"  stroke="var(--color-primary)" stroke-width="1"    opacity="0.35"/>
            <path d="M -20 300 Q 180  10 460 140" stroke="var(--color-primary)" stroke-width="0.75" opacity="0.25"/>
            <path d="M -20 350 Q 180  60 460 190" stroke="var(--color-primary)" stroke-width="0.5"  opacity="0.18"/>
        </svg>
    @endif

    {{ $slot }}
</div>
