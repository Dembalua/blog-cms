@extends('mail.layouts.base')

@section('subject', 'Broken affiliate links')

@section('accent', '#D97706')
@section('badge', 'Affiliate links')

@section('content')
    <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        {{ $broken->count() }} affiliate {{ Str::plural('link', $broken->count()) }} returned an unexpected status during the weekly check:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        @foreach ($broken as $link)
        <tr>
            <td style="padding: 10px 14px; border-bottom: 1px solid #EFE7FC; font-size:13px; vertical-align:top; width:40px; color:#DC2626; font-weight:600; font-family:monospace;">
                {{ $link->last_status_code === 0 ? 'ERR' : $link->last_status_code }}
            </td>
            <td style="padding: 10px 14px; border-bottom: 1px solid #EFE7FC; font-size:14px; vertical-align:top;">
                {{ $link->name }}
            </td>
            <td style="padding: 10px 14px; border-bottom: 1px solid #EFE7FC; font-size:12px; vertical-align:top; color:#6B7280; word-break:break-all;">
                {{ $link->url }}
            </td>
        </tr>
        @endforeach
    </table>

    <p style="margin:24px 0 0; font-size:14px; color:#374151; line-height:1.6;">
        Check the URLs in the CMS and deactivate or update any links that are no longer valid.
    </p>
@endsection
