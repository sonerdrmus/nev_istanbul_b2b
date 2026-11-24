<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('store.dealer_welcome_mail.heading') }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:system-ui,-apple-system,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 24px;">
                    <tr>
                        <td>
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;font-weight:600;">{{ config('app.name') }}</p>
                            <h1 style="margin:0 0 12px;font-size:20px;line-height:1.3;">{{ __('store.dealer_welcome_mail.heading') }}</h1>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
                                {{ __('store.dealer_welcome_mail.greeting', ['name' => $dealerRequest->applicantDisplayName()]) }}
                            </p>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
                                {{ __('store.dealer_welcome_mail.intro') }}
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.55;color:#334155;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;">
                                <tr>
                                    <td style="padding:12px 16px;width:140px;color:#64748b;">{{ __('store.dealer_welcome_mail.email_label') }}</td>
                                    <td style="padding:12px 16px;font-weight:600;color:#0f172a;">{{ $dealerRequest->email }}</td>
                                </tr>
                                @if(filled($dealerRequest->business_name))
                                <tr>
                                    <td style="padding:12px 16px;color:#64748b;border-top:1px solid #e2e8f0;">{{ __('store.dealer_welcome_mail.company_label') }}</td>
                                    <td style="padding:12px 16px;font-weight:600;color:#0f172a;border-top:1px solid #e2e8f0;">{{ $dealerRequest->business_name }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding:12px 16px;color:#64748b;border-top:1px solid #e2e8f0;">{{ __('store.dealer_welcome_mail.status_label') }}</td>
                                    <td style="padding:12px 16px;font-weight:600;color:#0f172a;border-top:1px solid #e2e8f0;">{{ __('store.dealer_welcome_mail.status_pending') }}</td>
                                </tr>
                            </table>

                            <p style="margin:18px 0 0;font-size:14px;line-height:1.6;color:#334155;">
                                {{ __('store.dealer_welcome_mail.next_steps') }}
                            </p>

                            <p style="margin:24px 0 0;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;background:#155fb3;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 20px;border-radius:10px;">
                                    {{ __('store.dealer_welcome_mail.cta') }}
                                </a>
                            </p>
                            <p style="margin:14px 0 0;font-size:12px;line-height:1.5;color:#94a3b8;word-break:break-all;">
                                {{ $loginUrl }}
                            </p>

                            <p style="margin:28px 0 0;font-size:13px;line-height:1.55;color:#64748b;">
                                {{ __('store.dealer_welcome_mail.footer') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
