<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $data['title'] }} {{ $data['order_number'] }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; margin: 18px 20px; }
        table { width: 100%; border-collapse: collapse; }
        td, th { vertical-align: top; }
        .grey { background: #808080; color: #fff; }
        .box td { border: 1px solid #111; padding: 4px 6px; }
        .logo { max-height: 40px; }
        .stamp { max-height: 88px; }
        .addr { font-size: 10px; line-height: 1.35; padding-top: 4px; }
        .meta-title { text-align: center; font-weight: bold; font-size: 12px; letter-spacing: 0.08em; padding: 7px; }
        .meta-label { width: 40%; font-weight: bold; font-size: 11px; }
        .meta-val { text-align: center; }
        .bar { background: #808080; color: #fff; font-weight: bold; padding: 5px 7px; }
        .bill { border: 1px solid #111; border-top: none; min-height: 72px; padding: 8px; font-weight: bold; line-height: 1.4; }
        .items th { background: #808080; color: #fff; font-size: 9px; text-transform: uppercase; padding: 6px; border: 1px solid #111; }
        .items td { border: 1px solid #111; padding: 5px 6px; }
        .num { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .total-bar td { background: #808080; color: #fff; font-weight: bold; font-size: 12px; padding: 7px; border: 1px solid #111; }
        .pay { margin-top: 8px; margin-bottom: 8px; font-size: 10px; }
        .bank-label { width: 120px; }
        .footer { margin-top: 10px; border-top: 2px double #111; padding-top: 6px; font-size: 9px; }
    </style>
</head>
<body>
    <table>
        <tr>
            <td style="width: 58%;">
                @if(!empty($data['logo_path']))
                    <img src="{{ $data['logo_path'] }}" class="logo" alt="logo">
                @endif
                @if(!empty($data['stamp_path']))
                    <img src="{{ $data['stamp_path'] }}" class="stamp" alt="stamp" style="margin-left: 18px;">
                @endif
                <div class="addr">
                    {{ $data['address_line_1'] }}<br>
                    {{ $data['address_line_2'] }}
                </div>
            </td>
            <td>
                <table class="box">
                    <tr><td class="grey meta-title" colspan="2">{{ $data['title'] }}</td></tr>
                    <tr>
                        <td class="meta-label">Date</td>
                        <td class="meta-val">{{ $data['date'] }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Invoice NR</td>
                        <td class="meta-val">{{ $data['invoice_number'] }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Order Nr.</td>
                        <td class="meta-val">{{ $data['order_number'] }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">Project Nr.</td>
                        <td class="meta-val">{{ $data['project_number'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="margin-top: 12px;">
        <tr>
            <td style="width: 58%; padding-right: 10px;">
                <div class="bar">Bill To</div>
                <div class="bill">{!! nl2br(e($data['bill_to_text'])) !!}</div>
            </td>
            <td>
                <table class="box">
                    <tr>
                        <td style="width: 45%;">PRODUCTION TIMES</td>
                        <td>{{ $data['production_times'] }}</td>
                    </tr>
                    <tr>
                        <td>Delivery Type:</td>
                        <td>{{ $data['delivery_type'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 46%;">ITEMS</th>
                <th class="center" style="width: 12%;">{{ __('store.proforma.qty') }}</th>
                <th class="center" style="width: 18%;">{{ __('store.proforma.unit_price') }}</th>
                <th class="center" style="width: 24%;">{{ $data['items_header_right'] }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $item)
                <tr>
                    <td>{{ $item['description'] }}</td>
                    <td class="num">{{ $item['qty_formatted'] }}</td>
                    <td class="num">{{ $item['unit_price_formatted'] }}</td>
                    <td class="num">{{ $data['currency_code'] }} {{ $item['amount_formatted'] }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2"></td>
                <td class="center"><strong>FOB - PRICE</strong></td>
                <td class="num">{{ $data['currency_code'] }} {{ $data['goods_formatted'] }}</td>
            </tr>
            <tr>
                <td colspan="2"><strong>SHIPPING PREFERENCE ;</strong> {{ $data['shipping_preference'] }}</td>
                <td class="center"><strong>SHIPPING COST</strong></td>
                <td class="num">{{ $data['currency_code'] }} {{ $data['shipping_formatted'] }}</td>
            </tr>
            <tr class="total-bar">
                <td colspan="3">TOTAL</td>
                <td class="num">{{ $data['currency_code'] }} {{ $data['total_formatted'] }}</td>
            </tr>
        </tbody>
    </table>

    <div class="pay">{{ $data['payment_line'] }}</div>

    <div class="bar">BANK DETAILS</div>
    <table style="margin-top: 0;">
        <tr>
            @if($data['primary_bank'])
                <td style="width: 50%; padding-right: 8px;">
                    <table>
                        <tr><td class="bank-label">Bank Name    :</td><td>{{ $data['primary_bank']['bank_name'] }}</td></tr>
                        <tr><td class="bank-label">Account Name:</td><td>{{ $data['primary_bank']['holder'] }}</td></tr>
                        <tr><td class="bank-label">Branch          :</td><td>{{ $data['primary_bank']['branch'] }}</td></tr>
                        <tr><td class="bank-label">Swift No       :</td><td>{{ $data['swift'] }}</td></tr>
                        <tr><td class="bank-label">Account No   :</td><td>{{ $data['primary_bank']['account_no'] }}</td></tr>
                        <tr><td class="bank-label">Iban No   TL      :</td><td>{{ $data['primary_bank']['iban_try'] }}</td></tr>
                        <tr><td class="bank-label">Iban N    Eur    :</td><td>{{ $data['primary_bank']['iban_eur'] }}</td></tr>
                        <tr><td class="bank-label">Iban No  Usd      :</td><td>{{ $data['primary_bank']['iban_usd'] }}</td></tr>
                    </table>
                </td>
            @endif
            @if($data['secondary_bank'])
                <td style="width: 50%;">
                    <table>
                        <tr><td class="bank-label">Bank Name    :</td><td>{{ $data['secondary_bank']['bank_name'] }}</td></tr>
                        <tr><td class="bank-label">Account Name:</td><td>{{ $data['secondary_bank']['holder'] }}</td></tr>
                        <tr><td class="bank-label">Branch          :</td><td>{{ $data['secondary_bank']['branch'] }}</td></tr>
                        <tr><td class="bank-label">Swift No       :</td><td>{{ $data['swift'] }}</td></tr>
                        <tr><td class="bank-label">Account No   :</td><td>{{ $data['secondary_bank']['account_no'] }}</td></tr>
                        <tr><td class="bank-label">Iban No   TL      :</td><td>{{ $data['secondary_bank']['iban_try'] }}</td></tr>
                        <tr><td class="bank-label">Iban N    Eur    :</td><td>{{ $data['secondary_bank']['iban_eur'] }}</td></tr>
                        <tr><td class="bank-label">Iban No  Usd      :</td><td>{{ $data['secondary_bank']['iban_usd'] }}</td></tr>
                    </table>
                </td>
            @endif
        </tr>
    </table>

    <div class="footer">
        Phone: {{ $data['company_phone'] }}
        &nbsp;&nbsp; E-mail: {{ $data['company_email'] }}
        &nbsp;/&nbsp; Web : {{ $data['company_web'] }}
    </div>
</body>
</html>
