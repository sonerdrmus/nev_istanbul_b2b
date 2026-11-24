<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.55;color:#334155;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:4px 16px;">
    <tr>
        <td style="padding:10px 0;width:150px;color:#64748b;">{{ __('store.order_mail.order_number') }}</td>
        <td style="padding:10px 0;font-weight:700;color:#0f172a;">{{ $order->order_number }}</td>
    </tr>
    <tr>
        <td style="padding:10px 0;color:#64748b;">{{ __('store.order_mail.status') }}</td>
        <td style="padding:10px 0;font-weight:600;color:#b45309;">{{ $statusLabel }}</td>
    </tr>
    <tr>
        <td style="padding:10px 0;color:#64748b;">{{ __('store.order_mail.customer') }}</td>
        <td style="padding:10px 0;font-weight:600;color:#0f172a;">{{ $order->customer_name }}</td>
    </tr>
    <tr>
        <td style="padding:10px 0;color:#64748b;">{{ __('store.order_mail.email') }}</td>
        <td style="padding:10px 0;color:#0f172a;">{{ $order->customer_email }}</td>
    </tr>
    @if(filled($order->customer_phone))
    <tr>
        <td style="padding:10px 0;color:#64748b;">{{ __('store.order_mail.phone') }}</td>
        <td style="padding:10px 0;color:#0f172a;">{{ $order->customer_phone }}</td>
    </tr>
    @endif
    @if(filled($order->customer_address))
    <tr>
        <td style="padding:10px 0;color:#64748b;vertical-align:top;">{{ __('store.order_mail.address') }}</td>
        <td style="padding:10px 0;color:#0f172a;">{{ $order->customer_address }}</td>
    </tr>
    @endif
    @if(filled($order->notes))
    <tr>
        <td style="padding:10px 0;color:#64748b;vertical-align:top;">{{ __('store.order_mail.order_note') }}</td>
        <td style="padding:10px 0;color:#0f172a;white-space:pre-wrap;">{{ $order->notes }}</td>
    </tr>
    @endif
</table>

<h2 style="margin:24px 0 10px;font-size:16px;color:#0f172a;">{{ __('store.order_mail.items_heading') }}</h2>
@foreach($items as $item)
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:12px;border:1px solid #e2e8f0;border-radius:12px;padding:12px 16px;">
        <tr>
            <td style="font-size:15px;font-weight:700;color:#0f172a;">{{ $item['name'] }}</td>
            <td align="right" style="font-size:14px;font-weight:700;color:#0f172a;white-space:nowrap;">{{ $item['subtotal'] }}</td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top:4px;font-size:13px;color:#64748b;">
                {{ __('store.order_mail.quantity') }}: {{ $item['quantity'] }} &middot; {{ __('store.order_mail.unit_price') }}: {{ $item['unit_price'] }}
            </td>
        </tr>
        @foreach($item['lines'] as $line)
            <tr>
                <td colspan="2" style="padding-top:3px;font-size:13px;color:#475569;">{{ $line }}</td>
            </tr>
        @endforeach
        @if($item['quick_notes'] !== '' || count($item['quick_images']) > 0)
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:10px 12px;">
                        <p style="margin:0 0 6px;font-size:11px;letter-spacing:0.06em;text-transform:uppercase;color:#64748b;font-weight:700;">{{ __('store.product.quick_order_summary') }}</p>
                        @if($item['quick_notes'] !== '')
                            <p style="margin:0 0 8px;font-size:13px;line-height:1.55;color:#0f172a;white-space:pre-wrap;">{{ $item['quick_notes'] }}</p>
                        @endif
                        @foreach($item['quick_images'] as $img)
                            <p style="margin:0 0 4px;font-size:13px;">
                                <a href="{{ $img['url'] }}" style="color:#155fb3;font-weight:600;">{{ __('store.order_mail.view_image', ['n' => $loop->iteration]) }}</a>
                            </p>
                        @endforeach
                    </div>
                </td>
            </tr>
        @endif
    </table>
@endforeach

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;font-size:14px;color:#334155;">
    @if((float) $order->shipping_cost > 0)
    <tr>
        <td style="padding:4px 0;color:#64748b;">{{ __('store.order_confirmation.shipping') }}</td>
        <td align="right" style="padding:4px 0;">{{ $shippingFormatted }}</td>
    </tr>
    @endif
    <tr>
        <td style="padding:6px 0;font-weight:700;color:#0f172a;">{{ __('store.order_confirmation.total') }}</td>
        <td align="right" style="padding:6px 0;font-weight:700;font-size:16px;color:#0f172a;">{{ $totalFormatted }}</td>
    </tr>
</table>
