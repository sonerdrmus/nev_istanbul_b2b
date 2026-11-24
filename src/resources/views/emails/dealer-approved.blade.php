<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('store.dealer_mail.heading') }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:system-ui,-apple-system,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 24px;">
                    <tr>
                        <td>
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;font-weight:600;">{{ config('app.name') }}</p>
                            <h1 style="margin:0 0 12px;font-size:20px;line-height:1.3;">{{ __('store.dealer_mail.heading') }}</h1>
                            <p style="margin:0 0 20px;font-size:15px;line-height:1.6;color:#334155;">
                                {{ __('store.dealer_mail.greeting', ['name' => $user->name]) }}
                            </p>
                            <p style="margin:0 0 16px;font-size:15px;line-height:1.6;color:#334155;">
                                {{ __('store.dealer_mail.intro') }}
                            </p>

                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.55;color:#334155;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:4px 16px;">
                                <tr>
                                    <td style="padding:10px 0;width:140px;color:#64748b;">{{ __('store.dealer_mail.email_label') }}</td>
                                    <td style="padding:10px 0;font-weight:600;color:#0f172a;">{{ $user->email }}</td>
                                </tr>
                                @if(filled($dealerRequest->business_name))
                                <tr>
                                    <td style="padding:10px 0;color:#64748b;">{{ __('store.dealer_mail.company_label') }}</td>
                                    <td style="padding:10px 0;font-weight:600;color:#0f172a;">{{ $dealerRequest->business_name }}</td>
                                </tr>
                                @endif
                                @if(filled($generatedPassword))
                                <tr>
                                    <td style="padding:10px 0;color:#64748b;">{{ __('store.dealer_mail.password_label') }}</td>
                                    <td style="padding:10px 0;font-weight:700;color:#0f172a;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;">{{ $generatedPassword }}</td>
                                </tr>
                                @endif
                            </table>

                            @if(filled($generatedPassword))
                                <p style="margin:16px 0 0;font-size:13px;line-height:1.55;color:#64748b;">
                                    {{ __('store.dealer_mail.password_generated_note') }}
                                </p>
                            @else
                                <p style="margin:16px 0 0;font-size:13px;line-height:1.55;color:#64748b;">
                                    {{ __('store.dealer_mail.password_own_note') }}
                                </p>
                            @endif

                            <p style="margin:24px 0 0;">
                                <a href="{{ $loginUrl }}" style="display:inline-block;background:#155fb3;color:#ffffff;text-decoration:none;font-weight:600;font-size:14px;padding:12px 20px;border-radius:10px;">
                                    {{ __('store.dealer_mail.cta') }}
                                </a>
                            </p>
                            <p style="margin:14px 0 0;font-size:12px;line-height:1.5;color:#94a3b8;word-break:break-all;">
                                {{ $loginUrl }}
                            </p>

                            <p style="margin:28px 0 0;font-size:13px;line-height:1.55;color:#64748b;">
                                {{ __('store.dealer_mail.footer') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
