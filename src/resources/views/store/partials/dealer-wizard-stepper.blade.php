@php
    $variant = $variant ?? 'rail';
    $wizardStepTitles = $wizardStepTitles ?? [];
@endphp

@if($variant === 'rail')
    <ol class="wiz-rail relative space-y-0">
        @foreach($wizardStepTitles as $stepNum => $shortTitle)
            <li class="flex gap-4">
                <div class="flex flex-col items-center">
                    <div
                        data-step-indicator="{{ $stepNum }}"
                        class="wiz-upcoming flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border text-sm font-semibold transition-all duration-200"
                    >{{ $stepNum }}</div>
                    @if(! $loop->last)
                        <div class="my-1 w-px flex-1 min-h-[1.5rem] bg-white/15" aria-hidden="true"></div>
                    @endif
                </div>
                <div class="min-w-0 {{ $loop->last ? 'pt-2.5' : 'pt-2.5 pb-5' }}">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.18em] text-white/40">0{{ $stepNum }}</p>
                    <p data-step-caption="{{ $stepNum }}" class="mt-1 text-[15px] font-medium leading-snug text-white/70">{{ $shortTitle }}</p>
                </div>
            </li>
        @endforeach
    </ol>
@else
    <div class="wiz-mobile">
        <div class="flex items-center">
            @foreach($wizardStepTitles as $stepNum => $_)
                @if($stepNum > 1)
                    <div class="h-0.5 min-w-3 flex-1 rounded-full bg-slate-200" aria-hidden="true"></div>
                @endif
                <div
                    data-step-indicator="{{ $stepNum }}"
                    class="wiz-upcoming flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border text-sm font-semibold transition-all duration-200"
                >{{ $stepNum }}</div>
            @endforeach
        </div>
        <div class="mt-3 flex justify-between gap-2">
            @foreach($wizardStepTitles as $stepNum => $shortTitle)
                <span data-step-caption="{{ $stepNum }}" class="min-w-0 flex-1 truncate text-center text-xs font-medium text-slate-400">{{ $shortTitle }}</span>
            @endforeach
        </div>
    </div>
@endif
