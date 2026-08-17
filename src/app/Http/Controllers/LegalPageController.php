<?php

namespace App\Http\Controllers;

use App\Models\LegalPage;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function show(string $slug): View
    {
        $page = LegalPage::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('store.legal-page', [
            'page' => $page,
        ]);
    }

    public function showContact(): View
    {
        $page = LegalPage::query()
            ->published()
            ->where('slug', LegalPage::CONTACT_SLUG)
            ->firstOrFail();

        return view('store.contact', [
            'page' => $page,
        ]);
    }
}
