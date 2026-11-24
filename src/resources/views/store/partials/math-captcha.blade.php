@php
    $captcha = \App\Support\MathCaptcha::challenge();
@endphp
<div>
    <label for="captcha_answer" class="{{ $labelClass ?? 'mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500' }}">
        {{ __('store.captcha.label') }} <span class="text-red-500">*</span>
    </label>
    <p class="mb-2 text-sm text-slate-600">{{ __('store.captcha.hint', ['question' => $captcha['question']]) }}</p>
    <input id="captcha_answer" name="captcha_answer" type="text" inputmode="numeric" required autocomplete="off"
           class="{{ $inputClass ?? 'w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-[15px] text-slate-900' }} @error('captcha_answer') border-red-400 @enderror">
    @error('captcha_answer')
        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
    @enderror
</div>
