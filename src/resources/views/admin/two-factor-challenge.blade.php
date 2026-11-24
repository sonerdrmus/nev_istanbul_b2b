<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA Doğrulama – Nevistanbul Admin</title>
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900 antialiased">
    <div class="mx-auto flex min-h-screen max-w-md items-center px-4 py-10">
        <div class="w-full rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <h1 class="text-xl font-semibold tracking-tight">Doğrulama kodu</h1>
            <p class="mt-2 text-sm text-slate-600">Authenticator uygulamanızdaki 6 haneli kodu girin.</p>
            @if($errors->any())
                <p class="mt-4 text-sm text-red-600">{{ $errors->first() }}</p>
            @endif
            <form method="POST" action="{{ route('admin.2fa.challenge.confirm') }}" class="mt-5 space-y-4">
                @csrf
                <div>
                    <label for="code" class="mb-1.5 block text-sm font-medium text-slate-700">Kod</label>
                    <input id="code" name="code" type="text" inputmode="numeric" autocomplete="one-time-code" required autofocus
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-200">
                </div>
                <button type="submit" class="w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white hover:bg-slate-800">
                    Doğrula
                </button>
            </form>
        </div>
    </div>
</body>
</html>
