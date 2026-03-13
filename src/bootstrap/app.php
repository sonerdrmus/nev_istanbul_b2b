<?php

use App\Http\Middleware\NormalizeLivewireUploadUrl;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        // Livewire dosya yüklemede imza 401 önlemi: istek URL'sini APP_URL ile eşleştir
        $middleware->web(append: [], prepend: [NormalizeLivewireUploadUrl::class]);
        // Mağaza (sepet, ödeme vb.) için giriş yapmamış kullanıcılar müşteri paneli girişine yönlendirilir
        $middleware->redirectGuestsTo(fn () => url('/panel/login'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Boş mesajlı HttpException (genelde 419 CSRF / oturum, dosya yükleme) için anlamlı mesaj
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getMessage() !== '') {
                return null;
            }
            $message = match ($e->getStatusCode()) {
                401 => 'Oturum sonlandı veya adres eşleşmiyor. Tarayıcıda kullandığınız adres (localhost / 127.0.0.1) .env APP_URL ile aynı olmalı. Sayfayı yenileyip tekrar giriş yapın.',
                419 => 'Oturum süresi doldu veya güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.',
                403 => 'Bu işlem için yetkiniz yok.',
                404 => 'Sayfa bulunamadı.',
                500 => 'Sunucu hatası. Lütfen daha sonra tekrar deneyin.',
                default => 'Bir hata oluştu (HTTP ' . $e->getStatusCode() . ').',
            };
            if ($request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest' || str_starts_with($request->path(), 'livewire')) {
                return response()->json(['message' => $message], $e->getStatusCode());
            }
            return null;
        });
    })->create();
