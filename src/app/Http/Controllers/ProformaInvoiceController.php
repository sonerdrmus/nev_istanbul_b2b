<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Order;
use App\Services\ProformaInvoiceService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProformaInvoiceController extends Controller
{
    public function pdf(Request $request, Order $order, ProformaInvoiceService $proforma)
    {
        $this->authorizeOrder($order);

        return $proforma->downloadPdf($order, $this->currency($request));
    }

    public function excel(Request $request, Order $order, ProformaInvoiceService $proforma): StreamedResponse
    {
        $this->authorizeOrder($order);

        return $proforma->downloadExcel($order, $this->currency($request));
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->isAccessibleBy(auth()->user()), 403);
    }

    private function currency(Request $request): Currency
    {
        $currencies = Currency::forCurrentUserWithTcmbSpot();
        $code = $request->query('currency', session('store_currency', 'TRY'));
        $selected = $currencies->firstWhere('code', $code)
            ?? $currencies->first()
            ?? Currency::getDefault();

        if ($selected) {
            session(['store_currency' => $selected->code]);

            return $selected;
        }

        return new Currency([
            'code' => 'TRY',
            'symbol' => '₺',
            'decimal_places' => 2,
            'exchange_rate' => 1,
        ]);
    }
}
