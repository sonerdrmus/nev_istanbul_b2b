<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('store.order_mail.customer_heading') }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:system-ui,-apple-system,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:600px;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 24px;">
                    <tr>
                        <td>
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;font-weight:600;">{{ config('app.name') }}</p>
                            <h1 style="margin:0 0 12px;font-size:20px;line-height:1.3;">{{ __('store.order_mail.customer_heading') }}</h1>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.6;color:#334155;">
                                {{ __('store.order_mail.customer_greeting', ['name' => $order->customer_name]) }}
                            </p>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#334155;">
                                {{ __('store.order_mail.customer_intro', ['number' => $order->order_number]) }}
                            </p>

                            @include('emails.partials.order-pending-summary')

                            <p style="margin:24px 0 0;font-size:13px;line-height:1.55;color:#64748b;">
                                {{ __('store.order_mail.customer_footer') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
