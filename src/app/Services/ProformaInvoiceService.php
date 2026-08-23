<?php

namespace App\Services;

use App\Models\BankAccount;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Support\LabelTypeVariationDisplay;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProformaInvoiceService
{
    private const GREY = '808080';

    /**
     * @return array<string, mixed>
     */
    public function payload(Order $order, Currency $currency): array
    {
        $order->loadMissing(['items', 'shippingMethod', 'bankAccount', 'user.company']);

        $goodsTry = (float) $order->items->sum(fn (OrderItem $item) => (float) $item->subtotal);
        $shippingTry = (float) $order->shipping_cost;
        $totalTry = (float) $order->total;
        $goods = $this->money($goodsTry, $currency);
        $shipping = $this->money($shippingTry, $currency);
        $total = $this->money($totalTry, $currency);
        $incoterm = $this->incoterm($order);
        $banks = $this->banks($order);

        return [
            'title' => 'PROFORMA INVOICE',
            'invoice_number' => 'PF-'.$order->order_number,
            'order_number' => $order->order_number,
            'project_number' => '',
            'production_times' => '',
            'date' => optional($order->created_at)->format('d.m.Y'),
            'company_name' => __('store.footer.company_name'),
            'address_line_1' => (string) config('store.proforma.address_line_1'),
            'address_line_2' => (string) config('store.proforma.address_line_2'),
            'company_phone' => __('store.footer.company_phone'),
            'company_email' => (string) config('store.proforma.accounting_email', __('store.footer.company_email')),
            'company_web' => __('store.contact.website'),
            'company_tax' => __('store.footer.company_tax'),
            'logo_path' => $this->imageDataUri('images/proforma-logo.png')
                ?? $this->imageDataUri('images/logo.png'),
            'stamp_path' => $this->imageDataUri('images/proforma-stamp.png'),
            'logo_file' => $this->imageFile('images/proforma-logo.png')
                ?? $this->imageFile('images/logo.png'),
            'stamp_file' => $this->imageFile('images/proforma-stamp.png'),
            'bill_to_name' => $order->customer_name,
            'bill_to_company' => $order->user?->company?->name,
            'bill_to_email' => $order->customer_email,
            'bill_to_phone' => $order->customer_phone,
            'bill_to_address' => $order->customer_address,
            'bill_to_text' => $this->billToText($order),
            'delivery_type' => $incoterm,
            'items_header_right' => trim($currency->symbol.' - '.strtolower($incoterm ?: 'exw')),
            'currency_code' => $currency->code,
            'currency_symbol' => $currency->symbol,
            'items' => $order->items->map(function (OrderItem $item) use ($currency) {
                $unit = $this->money((float) $item->price, $currency);
                $amount = $this->money((float) $item->subtotal, $currency);

                return [
                    'qty' => (int) $item->quantity,
                    'qty_formatted' => number_format((int) $item->quantity, 2, ',', '.'),
                    'description' => $this->itemDescription($item),
                    'unit_price' => $unit,
                    'unit_price_formatted' => $this->number($unit, $currency),
                    'amount' => $amount,
                    'amount_formatted' => $this->number($amount, $currency),
                ];
            })->all(),
            'goods' => $goods,
            'goods_formatted' => $this->number($goods, $currency),
            'shipping' => $shipping,
            'shipping_formatted' => $this->number($shipping, $currency),
            'has_shipping' => $shippingTry > 0 || (bool) $order->shipping_method_id,
            'shipping_preference' => $order->shippingMethod?->name ?: $incoterm,
            'total' => $total,
            'total_formatted' => $this->number($total, $currency),
            'total_in_words' => $this->amountInWords($total, $currency),
            'payment_line' => __('store.proforma.payment_line', [
                'amount' => $this->number($total, $currency).' '.$currency->code,
                'words' => $this->amountInWords($total, $currency),
                'number' => $order->order_number,
            ]),
            'notes' => $order->notes,
            'swift' => trim((string) config('store.proforma.swift', config('store.bank_transfer.swift', ''))),
            'banks' => $banks,
            'primary_bank' => $banks[0] ?? null,
            'secondary_bank' => $banks[1] ?? null,
        ];
    }

    public function downloadPdf(Order $order, Currency $currency)
    {
        $data = $this->payload($order, $currency);

        return Pdf::loadView('store.proforma.pdf', ['data' => $data])
            ->setPaper('a4', 'portrait')
            ->setOption('isRemoteEnabled', true)
            ->download('Proforma-'.$order->order_number.'.pdf');
    }

    public function downloadExcel(Order $order, Currency $currency): StreamedResponse
    {
        $data = $this->payload($order, $currency);
        $spreadsheet = $this->spreadsheet($data);
        $filename = 'Proforma-'.$order->order_number.'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function billToText(Order $order): string
    {
        $parts = array_filter([
            $order->user?->company?->name,
            $order->customer_name,
            $order->customer_address,
            $order->customer_email,
            $order->customer_phone ? 'Phone: '.$order->customer_phone : null,
        ]);

        return implode("\n", $parts);
    }

    private function itemDescription(OrderItem $item): string
    {
        $parts = [(string) $item->product_name];
        $variation = is_array($item->variation_data) ? $item->variation_data : [];

        if (! empty($variation['size_quantities']) && is_array($variation['size_quantities'])) {
            $sizes = [];
            foreach ($variation['size_quantities'] as $size => $qty) {
                if ((int) $qty > 0) {
                    $sizes[] = $size.': '.$qty;
                }
            }
            if ($sizes !== []) {
                $parts[] = __('store.order_confirmation.size_breakdown').' '.implode(', ', $sizes);
            }
        }

        foreach ($variation as $name => $value) {
            if (in_array($name, ['size_quantities', 'product_customization', 'product_customization_table', 'product_customization_notes', 'quick_order'], true)) {
                continue;
            }
            $display = LabelTypeVariationDisplay::formatVariationValue($value);
            if ($display) {
                $parts[] = $name.': '.$display;
            }
        }

        if (! empty($variation['quick_order']['notes'])) {
            $parts[] = (string) $variation['quick_order']['notes'];
        }

        return implode(' — ', $parts);
    }

    /**
     * @return list<array{bank_name: string, branch: ?string, holder: string, account_no: string, iban_try: string, iban_eur: string, iban_usd: string}>
     */
    private function banks(Order $order): array
    {
        /** @var Collection<int, BankAccount> $accounts */
        $accounts = BankAccount::query()->active()->orderBy('sort_order')->get();
        if ($accounts->isEmpty() && $order->bankAccount) {
            $accounts = collect([$order->bankAccount]);
        }

        return $accounts
            ->groupBy(fn (BankAccount $bank) => implode('|', [
                $bank->bank_name,
                $bank->account_holder,
                (string) $bank->branch,
            ]))
            ->map(function (Collection $group) {
                /** @var BankAccount $first */
                $first = $group->first();
                $byCurrency = $group->keyBy(fn (BankAccount $bank) => strtoupper((string) $bank->currency));

                return [
                    'bank_name' => $first->bank_name,
                    'branch' => $first->branch,
                    'holder' => $first->account_holder,
                    'account_no' => '',
                    'iban_try' => (string) ($byCurrency->get('TRY')?->iban ?? $byCurrency->get('TL')?->iban ?? ''),
                    'iban_eur' => (string) ($byCurrency->get('EUR')?->iban ?? ''),
                    'iban_usd' => (string) ($byCurrency->get('USD')?->iban ?? ''),
                ];
            })
            ->values()
            ->take(2)
            ->all();
    }

    private function incoterm(Order $order): string
    {
        $method = strtoupper(trim((string) ($order->shippingMethod?->name ?? '')));
        if ($method === '') {
            return 'EXW';
        }

        foreach (['EXW', 'FOB', 'CIF', 'DAP', 'DDP', 'CFR', 'FCA'] as $code) {
            if (str_contains($method, $code)) {
                return $code;
            }
        }

        return $order->shippingMethod?->name ?: 'EXW';
    }

    private function money(float $tryAmount, Currency $currency): float
    {
        $decimals = (int) ($currency->decimal_places ?: 2);

        return round($currency->convertFromTRY($tryAmount), $decimals);
    }

    private function imageFile(string $relative): ?string
    {
        $path = public_path($relative);

        return is_file($path) ? $path : null;
    }

    private function imageDataUri(string $relative): ?string
    {
        $path = $this->imageFile($relative);
        if (! $path) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode((string) file_get_contents($path));
    }

    private function number(float $amount, Currency $currency): string
    {
        return number_format($amount, (int) ($currency->decimal_places ?: 2), ',', '.');
    }

    private function amountInWords(float $amount, Currency $currency): string
    {
        $decimals = (int) ($currency->decimal_places ?: 2);
        $whole = (int) floor($amount);
        $fraction = (int) round(($amount - $whole) * (10 ** $decimals));
        $locale = app()->getLocale();
        $currencyName = match (strtoupper($currency->code)) {
            'EUR' => $locale === 'tr' ? 'Euro' : ($locale === 'it' ? 'Euro' : 'Euro'),
            'USD' => $locale === 'tr' ? 'Amerikan Doları' : ($locale === 'it' ? 'Dollari USA' : 'US Dollars'),
            'TRY', 'TL' => $locale === 'tr' ? 'Türk Lirası' : ($locale === 'it' ? 'Lira turca' : 'Turkish Lira'),
            default => $currency->code,
        };

        $words = $locale === 'tr'
            ? $this->turkishIntegerWords($whole)
            : $this->englishIntegerWords($whole);

        $and = $locale === 'tr' ? 've' : ($locale === 'it' ? 'e' : 'and');
        $denom = str_pad('1', $decimals + 1, '0');

        if ($fraction > 0) {
            return trim($words.' '.$currencyName.' '.$and.' '.$fraction.'/'.$denom);
        }

        return trim($words.' '.$currencyName);
    }

    private function englishIntegerWords(int $number): string
    {
        if ($number === 0) {
            return 'Zero';
        }

        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $chunk = function (int $n) use (&$chunk, $ones, $tens): string {
            if ($n < 20) {
                return $ones[$n];
            }
            if ($n < 100) {
                return trim($tens[intdiv($n, 10)].' '.$ones[$n % 10]);
            }
            if ($n < 1000) {
                return trim($ones[intdiv($n, 100)].' Hundred'.($n % 100 ? ' '.$chunk($n % 100) : ''));
            }
            if ($n < 1000000) {
                return trim($chunk(intdiv($n, 1000)).' Thousand'.($n % 1000 ? ' '.$chunk($n % 1000) : ''));
            }

            return trim($chunk(intdiv($n, 1000000)).' Million'.($n % 1000000 ? ' '.$chunk($n % 1000000) : ''));
        };

        return $chunk($number);
    }

    private function turkishIntegerWords(int $number): string
    {
        if ($number === 0) {
            return 'Sıfır';
        }

        $ones = ['', 'Bir', 'İki', 'Üç', 'Dört', 'Beş', 'Altı', 'Yedi', 'Sekiz', 'Dokuz'];
        $tens = ['', 'On', 'Yirmi', 'Otuz', 'Kırk', 'Elli', 'Altmış', 'Yetmiş', 'Seksen', 'Doksan'];

        $chunk = function (int $n) use (&$chunk, $ones, $tens): string {
            if ($n < 10) {
                return $ones[$n];
            }
            if ($n < 100) {
                return trim($tens[intdiv($n, 10)].' '.$ones[$n % 10]);
            }
            if ($n < 1000) {
                $h = intdiv($n, 100);
                $prefix = $h === 1 ? 'Yüz' : $ones[$h].' Yüz';

                return trim($prefix.($n % 100 ? ' '.$chunk($n % 100) : ''));
            }
            if ($n < 1000000) {
                $t = intdiv($n, 1000);
                $prefix = $t === 1 ? 'Bin' : $chunk($t).' Bin';

                return trim($prefix.($n % 1000 ? ' '.$chunk($n % 1000) : ''));
            }

            return trim($chunk(intdiv($n, 1000000)).' Milyon'.($n % 1000000 ? ' '.$chunk($n % 1000000) : ''));
        };

        return $chunk($number);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function spreadsheet(array $data): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(10);
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Invoice1');

        $greyFill = [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => self::GREY],
        ];
        $thin = Border::BORDER_THIN;
        $medium = Border::BORDER_MEDIUM;

        foreach (['A' => 26, 'B' => 12, 'C' => 18, 'D' => 14, 'E' => 18, 'F' => 16] as $col => $width) {
            $sheet->getColumnDimension($col)->setWidth($width);
        }

        if ($data['logo_file']) {
            $logo = new Drawing();
            $logo->setPath($data['logo_file']);
            $logo->setCoordinates('A1');
            $logo->setHeight(38);
            $logo->setWorksheet($sheet);
        }
        if ($data['stamp_file']) {
            $stamp = new Drawing();
            $stamp->setPath($data['stamp_file']);
            $stamp->setCoordinates('C1');
            $stamp->setHeight(90);
            $stamp->setWorksheet($sheet);
        }

        $sheet->mergeCells('E1:F1');
        $sheet->setCellValue('E1', $data['title']);
        $sheet->getStyle('E1:F1')->getFill()->applyFromArray($greyFill);
        $sheet->getStyle('E1')->getFont()->setBold(true)->setSize(11)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E1:F1')->getBorders()->getAllBorders()->setBorderStyle($medium);

        $sheet->setCellValue('E2', 'Date');
        $sheet->getStyle('E2')->getFont()->setBold(true)->setSize(12);
        $sheet->setCellValue('F2', $data['date']);
        $sheet->getStyle('F2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->mergeCells('A3:C4');
        $sheet->setCellValue('A3', $data['address_line_1']);
        $sheet->getStyle('A3')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);

        $sheet->setCellValue('E3', 'Invoice NR');
        $sheet->getStyle('E3')->getFont()->setBold(true)->setSize(12);
        $sheet->setCellValue('F3', $data['invoice_number']);
        $sheet->getStyle('F3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('E4', 'Order Nr.');
        $sheet->getStyle('E4')->getFont()->setBold(true)->setSize(12);
        $sheet->setCellValue('F4', $data['order_number']);
        $sheet->getStyle('F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A5', $data['address_line_2']);
        $sheet->setCellValue('E5', 'Project Nr.');
        $sheet->getStyle('E5')->getFont()->setBold(true)->setSize(12);
        $sheet->setCellValue('F5', $data['project_number']);

        $sheet->setCellValue('A9', 'Bill To');
        $sheet->getStyle('A9')->getFill()->applyFromArray($greyFill);
        $sheet->getStyle('A9')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A9')->getBorders()->getOutline()->setBorderStyle($medium);

        $sheet->mergeCells('A10:C13');
        $sheet->setCellValue('A10', $data['bill_to_text']);
        $sheet->getStyle('A10')->getAlignment()->setWrapText(true)->setVertical(Alignment::VERTICAL_TOP);
        $sheet->getStyle('A10:C13')->getBorders()->getOutline()->setBorderStyle($thin);

        $sheet->setCellValue('E11', 'PRODUCTION TIMES');
        $sheet->setCellValue('F11', $data['production_times']);
        $sheet->setCellValue('E12', 'Delivery Type:');
        $sheet->setCellValue('F12', $data['delivery_type']);

        $sheet->mergeCells('A14:C14');
        $sheet->setCellValue('A14', 'ITEMS');
        $sheet->mergeCells('D14:F14');
        $sheet->setCellValue('D14', $data['items_header_right']);
        $sheet->getStyle('A14:F14')->getFill()->applyFromArray($greyFill);
        $sheet->getStyle('A14:F14')->getFont()->setBold(true)->setSize(9)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D14')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A14:F14')->getBorders()->getAllBorders()->setBorderStyle($thin);

        $sheet->setCellValue('A15', __('store.proforma.description'));
        $sheet->setCellValue('D15', __('store.proforma.qty'));
        $sheet->setCellValue('E15', __('store.proforma.unit_price'));
        $sheet->setCellValue('F15', __('store.proforma.amount'));
        $sheet->getStyle('A15:F15')->getFont()->setBold(true);
        $sheet->getStyle('A15:F15')->getBorders()->getAllBorders()->setBorderStyle($thin);

        $row = 16;
        foreach ($data['items'] as $item) {
            $sheet->mergeCells('A'.$row.':C'.$row);
            $sheet->setCellValue('A'.$row, $item['description']);
            $sheet->setCellValue('D'.$row, $item['qty_formatted']);
            $sheet->setCellValue('E'.$row, $item['unit_price_formatted']);
            $sheet->setCellValue('F'.$row, $item['amount_formatted']);
            $sheet->getStyle('A'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('D'.$row.':F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('A'.$row.':F'.$row)->getBorders()->getAllBorders()->setBorderStyle($thin);
            $row++;
        }

        // Keep at least a few blank item rows like the template when few lines.
        while ($row < 20) {
            $sheet->mergeCells('A'.$row.':C'.$row);
            $sheet->getStyle('A'.$row.':F'.$row)->getBorders()->getAllBorders()->setBorderStyle($thin);
            $row++;
        }

        $sheet->setCellValue('D'.$row, 'FOB - PRICE');
        $sheet->getStyle('D'.$row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('F'.$row, $data['goods_formatted']);
        $sheet->getStyle('F'.$row)->getBorders()->getAllBorders()->setBorderStyle($thin);
        $sheet->getStyle('F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $fobRow = $row;
        $row++;

        $sheet->setCellValue('A'.$row, 'SHIPPING PREFERENCE ;');
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(9);
        $sheet->getStyle('A'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->setCellValue('B'.$row, $data['shipping_preference']);
        $sheet->setCellValue('D'.$row, 'SHIPPING COST');
        $sheet->getStyle('D'.$row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('D'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->setCellValue('F'.$row, $data['shipping_formatted']);
        $sheet->getStyle('F'.$row)->getBorders()->getAllBorders()->setBorderStyle($thin);
        $sheet->getStyle('F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $shipRow = $row;
        $row++;

        $sheet->setCellValue('E'.$row, 'TOTAL');
        $sheet->setCellValue('F'.$row, $data['total_formatted']);
        $sheet->getStyle('E'.$row.':F'.$row)->getFill()->applyFromArray($greyFill);
        $sheet->getStyle('E'.$row)->getFont()->setSize(12)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('F'.$row)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('E'.$row.':F'.$row)->getBorders()->getAllBorders()->setBorderStyle($thin);
        $sheet->getStyle('F'.$row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E'.$row.':F'.$row)->getBorders()->getTop()->setBorderStyle($medium);
        $sheet->getStyle('E'.$row.':F'.$row)->getBorders()->getBottom()->setBorderStyle($medium);
        $totalRow = $row;
        $row++;

        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->setCellValue('A'.$row, $data['payment_line']);
        $sheet->getStyle('A'.$row)->getAlignment()->setWrapText(true);
        $row++;

        $sheet->setCellValue('A'.$row, 'BANK DETAILS');
        $sheet->getStyle('A'.$row)->getFill()->applyFromArray($greyFill);
        $sheet->getStyle('A'.$row)->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('FFFFFF');
        $bankStart = $row;
        $row++;

        $this->writeBankBlock($sheet, $row, $data['primary_bank'] ?? null, $data['swift'], 'A', 'B');
        if (! empty($data['secondary_bank'])) {
            $this->writeBankBlock($sheet, $bankStart + 1, $data['secondary_bank'], $data['swift'], 'D', 'E');
        }

        $row = $bankStart + 9;
        $sheet->mergeCells('A'.$row.':F'.$row);
        $sheet->setCellValue(
            'A'.$row,
            'Phone: '.$data['company_phone'].'   E-mail: '.$data['company_email'].' / Web : '.$data['company_web']
        );
        $sheet->getStyle('A'.$row)->getBorders()->getTop()->setBorderStyle(Border::BORDER_DOUBLE);

        // Silence unused vars while documenting formula positions.
        unset($fobRow, $shipRow, $totalRow);

        return $spreadsheet;
    }

    /**
     * @param  array{bank_name: string, branch: ?string, holder: string, account_no: string, iban_try: string, iban_eur: string, iban_usd: string}|null  $bank
     */
    private function writeBankBlock($sheet, int $row, ?array $bank, string $swift, string $labelCol, string $valueCol): void
    {
        if (! $bank) {
            return;
        }

        $lines = [
            ['Bank Name    :', $bank['bank_name']],
            ['Account Name:', $bank['holder']],
            ['Branch          :', $bank['branch'] ?: ''],
            ['Swift No       :', $swift],
            ['Account No   :', $bank['account_no']],
            ['Iban No   TL      :', $bank['iban_try']],
            ['Iban N    Eur    :', $bank['iban_eur']],
            ['Iban No  Usd      :', $bank['iban_usd']],
        ];

        foreach ($lines as $i => [$label, $value]) {
            $r = $row + $i;
            $sheet->setCellValue($labelCol.$r, $label);
            $sheet->setCellValue($valueCol.$r, $value);
            if ($labelCol === 'A') {
                $sheet->mergeCells($valueCol.$r.':C'.$r);
            }
        }
    }
}
