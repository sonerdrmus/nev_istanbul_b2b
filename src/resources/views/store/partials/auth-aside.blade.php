@php
    $highlights = $highlights ?? [];
    $ctaUrl = $ctaUrl ?? null;
    $ctaPrefix = $ctaPrefix ?? null;
    $ctaLabel = $ctaLabel ?? null;
@endphp

<aside class="relative overflow-hidden rounded-3xl bg-slate-950 text-white shadow-[0_24px_60px_-32px_rgba(15,23,42,0.55)] lg:min-h-[32rem]">
    <div class="pointer-events-none absolute inset-0" aria-hidden="true">
        <div class="absolute -left-16 -top-20 h-64 w-64 rounded-full bg-primary-500/35 blur-3xl"></div>
        <div class="absolute right-[-3rem] top-1/3 h-72 w-72 rounded-full bg-sky-400/20 blur-3xl"></div>
        <div class="absolute -bottom-24 left-1/3 h-56 w-56 rounded-full bg-primary-700/40 blur-3xl"></div>
        <div class="absolute inset-0 opacity-[0.18]" style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.45) 1px, transparent 0); background-size: 22px 22px;"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950/20 via-transparent to-primary-950/40"></div>
    </div>

    <div class="relative flex h-full flex-col justify-between gap-8 px-6 py-7 sm:px-8 sm:py-9 lg:px-9 lg:py-10">
        <div>
            <p class="text-[11px] font-semibold uppercase tracking-[0.22em] text-primary-300">{{ $kicker }}</p>
            <h1 class="mt-3 text-2xl sm:text-[1.85rem] font-semibold tracking-tight leading-tight">{{ $title }}</h1>
            <p class="mt-3 text-sm sm:text-[0.95rem] leading-relaxed text-slate-300">{{ $lead }}</p>
        </div>

        @if($highlights !== [])
            <ul class="space-y-3.5">
                @foreach($highlights as $item)
                    <li class="flex gap-3.5">
                        <span class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-white/10 ring-1 ring-white/10">
                            <svg class="h-4 w-4 text-primary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-white">{{ $item['title'] }}</span>
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-400">{{ $item['text'] }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif

        @if($ctaUrl)
            <p class="text-sm text-slate-400">
                {{ $ctaPrefix }}
                <a href="{{ $ctaUrl }}" class="font-semibold text-white underline decoration-primary-400/80 underline-offset-4 hover:text-primary-200 transition-colors">{{ $ctaLabel }}</a>
            </p>
        @endif
    </div>
</aside>
