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
        .addr { font-size: 10px; line-height: 1.35; padding-top: 4px; }
        .meta-title { text-align: center; font-weight: bold; font-size: 12px; letter-spacing: 0.04em; padding: 7px; }
        .meta-label { width: 40%; font-weight: bold; font-size: 11px; }
        .meta-val { text-align: center; }
        .bar { background: #808080; color: #fff; font-weight: bold; padding: 5px 7px; }
        .bill { border: 1px solid #111; border-top: none; min-height: 72px; padding: 8px; font-weight: bold; line-height: 1.4; }
        .items th { background: #808080; color: #fff; font-size: 9px; text-transform: uppercase; padding: 6px; border: 1px solid #111; }
        .items td { border: 1px solid #111; padding: 5px 6px; }
        .num { text-align: right; white-space: nowrap; }
        .center { text-align: center; }
        .detail { margin: 0; padding-left: 14px; }
        .detail li { margin: 0 0 2px; }
        .group-title { font-weight: bold; margin-top: 4px; }
        .notes { margin-top: 10px; border: 1px solid #111; padding: 8px; white-space: pre-wrap; }
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
                <div class="addr">
                    {{ $data['address_line_1'] }}<br>
                    {{ $data['address_line_2'] }}
                </div>
            </td>
            <td>
                <table class="box">
                    <tr><td class="grey meta-title" colspan="2">{{ $data['title'] }}</td></tr>
                    <tr>
                        <td class="meta-label">{{ __('store.proforma.date') }}</td>
                        <td class="meta-val">{{ $data['date'] }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">{{ __('store.proforma.order_nr') }}</td>
                        <td class="meta-val">{{ $data['order_number'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table style="margin-top: 12px;">
        <tr>
            <td style="width: 58%; padding-right: 10px;">
                <div class="bar">{{ __('store.proforma.bill_to') }}</div>
                <div class="bill">{!! nl2br(e($data['bill_to_text'])) !!}</div>
            </td>
            <td>
                <table class="box">
                    <tr>
                        <td style="width: 45%;">{{ __('store.proforma.delivery_type') }}</td>
                        <td>{{ $data['delivery_type'] }}</td>
                    </tr>
                    <tr>
                        <td>{{ __('store.proforma.shipping_preference') }}</td>
                        <td>{{ $data['shipping_preference'] }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="items" style="margin-top: 10px;">
        <thead>
            <tr>
                <th style="width: 46%;">{{ __('store.proforma.items') }}</th>
                <th class="center" style="width: 12%;">{{ __('store.proforma.qty') }}</th>
                <th class="center" style="width: 18%;">{{ __('store.proforma.unit_price') }}</th>
                <th class="center" style="width: 24%;">{{ __('store.proforma.amount') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['items'] as $item)
                <tr>
                    <td>
                        <strong>{{ $item['description'] }}</strong>
                        @foreach($item['groups'] as $group)
                            <div class="group-title">{{ $group['title'] }}</div>
                            <ul class="detail">
                                @foreach($group['lines'] as $line)
                                    <li>{{ $line }}</li>
                                @endforeach
                            </ul>
                        @endforeach
                    </td>
                    <td class="num">{{ $item['qty_formatted'] }}</td>
                    <td class="num">{{ $item['unit_price_formatted'] }}</td>
                    <td class="num">{{ $data['currency_code'] }} {{ $item['amount_formatted'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if(!empty($data['notes']))
        <div class="notes">
            <strong>{{ __('store.order_form.notes') }}</strong><br>
            {{ $data['notes'] }}
        </div>
    @endif

    <div class="footer">
        {{ __('store.proforma.phone') }}: {{ $data['company_phone'] }}
        &nbsp;&nbsp; {{ __('store.proforma.email') }}: {{ $data['company_email'] }}
        &nbsp;/&nbsp; {{ __('store.proforma.web') }}: {{ $data['company_web'] }}
    </div>
</body>
</html>
