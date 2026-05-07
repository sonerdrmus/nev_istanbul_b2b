<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetStoreLocale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function switch(Request $request, string $locale): RedirectResponse
    {
        if (! in_array($locale, SetStoreLocale::SUPPORTED, true)) {
            abort(404);
        }

        session(['locale' => $locale]);

        return redirect()->back(fallback: route('home'));
    }
}
