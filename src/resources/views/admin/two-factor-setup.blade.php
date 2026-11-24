<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA Kurulum – Nevistanbul Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="mx-auto flex min-h-screen max-w-lg items-center px-4 py-10">
        <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-xl font-semibold tracking-tight">İki adımlı doğrulama</h1>
            <p class="mt-2 text-sm text-slate-600">Authenticator uygulamanıza şu anahtarı ekleyin, ardından 6 haneli kodu girin.</p>
            <div class="mt-5 rounded-xl bg-slate-50 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Gizli anahtar</p>
                <p class="mt-1 break-all font-mono text-sm font-semibold text-slate-900">{{ $secret }}</p>
                <p class="mt-3 break-all text-xs text-slate-500">{{ $otpAuthUrl }}</p>
            </div>
            @if($errors->any())
                <p class="mt-4 text-sm text-red-600">{{ $errors->first() }}</p>
            @endif
            <form method="POST" action="{{ route('admin.2fa.setup.confirm') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="code" class="mb-1.5 block text-sm font-medium text-slate-700">Doğrulama kodu</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                </div>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                    Etkinleştir ve devam et
                </button>
            </form>
        </div>
    </div>
</body>
</html>
