@extends('store.layout')

@section('title', 'Bayi Ol')

@section('content')
    {{-- Neden Bayi Olmalı: bilgi ve avantajlar --}}
    <div class="max-w-4xl mx-auto mb-12">
        <div class="rounded-2xl bg-gradient-to-br from-primary-50 to-slate-50 border border-primary-100 overflow-hidden">
            <div class="px-6 sm:px-10 py-8 sm:py-10">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-14 h-14 rounded-2xl bg-primary-500 flex items-center justify-center text-white shadow-lg">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">Neden Bayi Olmalı?</h1>
                        <p class="text-slate-600 mt-0.5">B2B iş ortağımız olun, avantajlardan yararlanın</p>
                    </div>
                </div>
                <div class="prose prose-slate max-w-none text-slate-700 space-y-4">
                    <p class="text-base leading-relaxed">
                        Bayi ağımıza katılarak toptan fiyat avantajları, özel indirimler ve sipariş kolaylığından yararlanın.
                        Onaylanan bayilerimiz panel üzerinden sipariş verebilir, stok takibi yapabilir ve fiyatları görüntüleyebilir.
                    </p>
                    <ul class="grid sm:grid-cols-2 gap-3 list-none pl-0 space-y-0">
                        <li class="flex items-start gap-3 p-4 rounded-xl bg-white/80 border border-slate-100">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span><strong class="text-slate-900">Toptan fiyat avantajı</strong> – Bayi fiyatlarıyla alım</span>
                        </li>
                        <li class="flex items-start gap-3 p-4 rounded-xl bg-white/80 border border-slate-100">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span><strong class="text-slate-900">Online sipariş paneli</strong> – 7/24 sipariş verebilirsiniz</span>
                        </li>
                        <li class="flex items-start gap-3 p-4 rounded-xl bg-white/80 border border-slate-100">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span><strong class="text-slate-900">Havale / EFT ile ödeme</strong> – Güvenli ödeme seçenekleri</span>
                        </li>
                        <li class="flex items-start gap-3 p-4 rounded-xl bg-white/80 border border-slate-100">
                            <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <span><strong class="text-slate-900">Özel müşteri grubu indirimleri</strong> – Grup bazlı fiyatlandırma</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Bayilik Başvuru Formu (modern UI) --}}
    <div class="max-w-3xl mx-auto">
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 sm:px-8 py-6 border-b border-slate-200 bg-slate-50/50">
                <h2 class="text-xl font-bold text-slate-900">Bayilik Başvuru Formu</h2>
                <p class="text-sm text-slate-600 mt-1">Formu doldurarak başvurunuzu iletebilirsiniz. İnceleme sonrası sizinle iletişime geçilecektir.</p>
            </div>
            <form action="{{ route('dealer-requests.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
                @csrf
                @if($errors->any())
                    <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="full_name" class="block text-sm font-medium text-slate-700 mb-1.5">Ad Soyad <span class="text-red-500">*</span></label>
                        <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('full_name') border-red-500 @enderror"
                            placeholder="Adınız Soyadınız">
                    </div>
                    <div>
                        <label for="tc_no" class="block text-sm font-medium text-slate-700 mb-1.5">T.C. Kimlik No <span class="text-red-500">*</span></label>
                        <input id="tc_no" name="tc_no" type="text" value="{{ old('tc_no') }}" required maxlength="11" minlength="11" inputmode="numeric"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('tc_no') border-red-500 @enderror"
                            placeholder="11 haneli T.C. kimlik no">
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-1.5">E-posta <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('email') border-red-500 @enderror"
                            placeholder="ornek@email.com">
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-slate-700 mb-1.5">Telefon</label>
                        <input id="phone" name="phone" type="text" value="{{ old('phone') }}"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('phone') border-red-500 @enderror"
                            placeholder="05XX XXX XX XX">
                    </div>
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-slate-700 mb-1.5">Adres</label>
                    <textarea id="address" name="address" rows="3"
                        class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-colors @error('address') border-red-500 @enderror"
                        placeholder="Adresiniz (isteğe bağlı)">{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Dosya Yükle (PDF)</label>
                        <input name="document_pdf" type="file" accept="application/pdf"
                            class="block w-full text-sm text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:font-medium file:cursor-pointer transition-colors">
                        <p class="text-xs text-slate-500 mt-1.5">PDF, en fazla 5MB</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Dosya Yükle (JPEG)</label>
                        <input name="document_jpeg" type="file" accept="image/jpeg"
                            class="block w-full text-sm text-slate-600 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:font-medium file:cursor-pointer transition-colors">
                        <p class="text-xs text-slate-500 mt-1.5">JPEG, en fazla 5MB</p>
                    </div>
                </div>

                <div class="pt-4 flex flex-col sm:flex-row gap-3 justify-end">
                    <a href="{{ route('home') }}" class="order-2 sm:order-1 px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-medium hover:bg-slate-50 text-center transition-colors">Vazgeç</a>
                    <button type="submit" class="order-1 sm:order-2 px-6 py-3 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-semibold shadow-sm hover:shadow transition-all">
                        Başvuruyu Gönder
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
