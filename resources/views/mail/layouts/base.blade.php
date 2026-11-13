@php
    use App\Models\Setting;
    $primary    = Setting::get('theme.light_primary')    ?? '#7C3AED';
    $background = Setting::get('theme.light_background') ?? '#FAF5FF';
    $border     = Setting::get('theme.light_border')     ?? '#EFE7FC';
    $text       = Setting::get('theme.light_text')       ?? '#0F172A';
    $textMuted  = Setting::get('theme.light_text_muted') ?? '#374151';
    $textSubtle = Setting::get('theme.light_text_subtle') ?? '#6B7280';
    $siteName   = Setting::get('site.name') ?? config('app.name');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('subject', $siteName)</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&display=swap');
    </style>
</head>
<body style="margin:0; padding:0; background-color:{{ $background }}; font-family:'Raleway', Arial, sans-serif; color:{{ $text }};">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:{{ $background }};">
        <tr>
            <td align="center" style="padding: 40px 16px;">

                {{-- Card --}}
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:12px; overflow:hidden; border:1px solid {{ $border }};">

                    {{-- Header --}}
                    <tr>
                        <td style="background-color:{{ $primary }}; padding: 28px 36px;">
                            <span style="font-family:'Raleway', Arial, sans-serif; font-size:20px; font-weight:700; color:#ffffff; letter-spacing:0.02em;">
                                {{ $siteName }}
                            </span>
                        </td>
                    </tr>

                    {{-- Accent bar --}}
                    @hasSection('accent')
                    <tr>
                        <td style="background-color:@yield('accent'); padding: 12px 36px;">
                            <span style="font-family:'Raleway', Arial, sans-serif; font-size:13px; font-weight:600; color:#ffffff; letter-spacing:0.05em; text-transform:uppercase;">
                                @yield('badge')
                            </span>
                        </td>
                    </tr>
                    @endif

                    {{-- Content --}}
                    <tr>
                        <td style="padding: 36px;">
                            @yield('content')
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 36px; border-top: 1px solid {{ $border }};">
                            <p style="margin:0; font-size:12px; color:{{ $textSubtle }}; font-family:'Raleway', Arial, sans-serif;">
                                {{ $siteName }} &mdash; {{ now()->format('d.m.Y, H:i') }}
                            </p>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>
</body>
</html>
