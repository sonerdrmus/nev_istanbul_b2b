<?php

namespace App\Http\Controllers;

use App\Support\StoreLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! StoreLocale::isSupported($locale)) {
            abort(404);
        }

        StoreLocale::apply($locale);

        $referer = $request->headers->get('referer');
        if (is_string($referer) && $referer !== '') {
            try {
                $refRequest = Request::create($referer);

                return redirect()->to(StoreLocale::alternateUrl($locale, $refRequest));
            } catch (\Throwable) {
                // fall through
            }
        }

        return redirect()->route('home', ['locale' => $locale]);
    }
}
