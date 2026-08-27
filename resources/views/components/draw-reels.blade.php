@props([
    'digitLength' => 6,
    'idPrefix' => 'draw',
    'firstDigitMax' => 2,
])

<div class="draw-reels" id="{{ $idPrefix }}-reels" data-digit-length="{{ $digitLength }}" data-first-digit-max="{{ $firstDigitMax }}">
    <div class="draw-reels__grid">
        @for ($i = 0; $i < $digitLength; $i++)
            @php
                $alphabetSize = $i === 0 ? ($firstDigitMax + 1) : 10;
            @endphp
            <div class="draw-reel" data-index="{{ $i }}" data-alphabet="{{ $alphabetSize }}">
                <div class="draw-reel__window">
                    <div class="draw-reel__strip" data-strip>
                        @for ($n = 0; $n < 30; $n++)
                            <span class="draw-reel__digit">{{ $n % $alphabetSize }}</span>
                        @endfor
                    </div>
                </div>
                <div class="draw-reel__label">D{{ $i + 1 }}</div>
            </div>
        @endfor
    </div>
</div>

<style>
    .draw-reels__grid {
        display: grid;
        grid-template-columns: repeat({{ $digitLength }}, minmax(0, 1fr));
        gap: 0.65rem;
        width: 100%;
        max-width: 42rem;
        margin-inline: auto;
    }
    .draw-reel {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.45rem;
    }
    .draw-reel__window {
        position: relative;
        width: 100%;
        aspect-ratio: 0.72;
        border-radius: 1rem;
        border: 1px solid var(--border-color);
        background: linear-gradient(180deg, color-mix(in srgb, var(--bg-primary) 70%, #000), var(--bg-card));
        overflow: hidden;
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--accent) 18%, transparent), 0 10px 30px rgba(0,0,0,.18);
    }
    .draw-reel__window::before,
    .draw-reel__window::after {
        content: '';
        position: absolute;
        left: 0; right: 0;
        height: 28%;
        z-index: 2;
        pointer-events: none;
    }
    .draw-reel__window::before {
        top: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,.35), transparent);
    }
    .draw-reel__window::after {
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,.35), transparent);
    }
    .draw-reel__strip {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        transition: transform 2.4s cubic-bezier(.12,.7,.16,1);
        will-change: transform;
    }
    .draw-reel.is-spinning .draw-reel__strip {
        transition: none;
        animation: draw-spin 0.35s linear infinite;
    }
    .draw-reel__digit {
        flex: 0 0 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Space Grotesk', sans-serif;
        font-weight: 700;
        font-size: clamp(1.8rem, 6vw, 3.4rem);
        color: var(--text-primary);
        letter-spacing: -0.04em;
    }
    .draw-reel.is-locked .draw-reel__window {
        border-color: var(--accent);
        box-shadow: 0 0 0 2px color-mix(in srgb, var(--accent) 45%, transparent), inset 0 0 24px color-mix(in srgb, var(--accent) 18%, transparent);
    }
    .draw-reel.is-locked .draw-reel__digit {
        color: var(--accent);
    }
    .draw-reel__label {
        font-size: 0.65rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--text-secondary);
    }
    @keyframes draw-spin {
        from { transform: translateY(0); }
        to { transform: translateY(-100%); }
    }
    @media (max-width: 640px) {
        .draw-reels__grid { gap: 0.4rem; }
        .draw-reel__window { border-radius: 0.75rem; }
    }
</style>
