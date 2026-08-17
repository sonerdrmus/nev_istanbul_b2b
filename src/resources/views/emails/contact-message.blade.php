<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('store.contact.mail_heading') }}</title>
</head>
<body style="margin:0;padding:0;background:#f1f5f9;font-family:system-ui,-apple-system,sans-serif;color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px;background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:28px 24px;">
                    <tr>
                        <td>
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;font-weight:600;">{{ config('app.name') }}</p>
                            <h1 style="margin:0 0 20px;font-size:20px;line-height:1.3;">{{ __('store.contact.mail_heading') }}</h1>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px;line-height:1.55;color:#334155;">
                                <tr>
                                    <td style="padding:6px 0;width:140px;color:#64748b;">{{ __('store.contact.name') }}</td>
                                    <td style="padding:6px 0;font-weight:600;color:#0f172a;">{{ $senderName }}</td>
                                </tr>
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">{{ __('store.contact.email') }}</td>
                                    <td style="padding:6px 0;"><a href="mailto:{{ $senderEmail }}" style="color:#155fb3;">{{ $senderEmail }}</a></td>
                                </tr>
                                @if($phone)
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">{{ __('store.contact.phone') }}</td>
                                    <td style="padding:6px 0;">{{ $phone }}</td>
                                </tr>
                                @endif
                                @if($companyName)
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">{{ __('store.contact.company') }}</td>
                                    <td style="padding:6px 0;">{{ $companyName }}</td>
                                </tr>
                                @endif
                                @if($topic)
                                <tr>
                                    <td style="padding:6px 0;color:#64748b;">{{ __('store.contact.subject') }}</td>
                                    <td style="padding:6px 0;">{{ $topic }}</td>
                                </tr>
                                @endif
                            </table>
                            <p style="margin:20px 0 8px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;font-weight:600;">{{ __('store.contact.message') }}</p>
                            <div style="white-space:pre-wrap;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px 16px;font-size:14px;line-height:1.65;color:#0f172a;">{{ $bodyText }}</div>
                            @if(count($files) > 0)
                                <p style="margin:20px 0 0;font-size:13px;color:#64748b;">{{ __('store.contact.mail_attachments_count', ['count' => count($files)]) }}</p>
                            @endif
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
