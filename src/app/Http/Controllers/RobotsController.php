<?php

namespace App\Http\Controllers;

use App\Support\StoreLocale;
use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /sepet',
            'Disallow: /odeme',
            'Disallow: /hesabim',
            'Disallow: /siparis',
            'Disallow: /giris',
            'Disallow: /store-login',
            'Disallow: /cikis',
            'Disallow: /livewire',
            'Disallow: /panel',
            'Disallow: /api/',
        ];

        foreach (StoreLocale::supported() as $locale) {
            foreach (['/sepet', '/odeme', '/hesabim', '/siparis', '/giris', '/store-login', '/cikis'] as $path) {
                $lines[] = 'Disallow: /'.$locale.$path;
            }
        }

        $lines[] = '';
        $lines[] = 'Sitemap: '.url('/sitemap.xml');
        $lines[] = '';

        return response(implode("\n", $lines), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
