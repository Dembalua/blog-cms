@extends('mail.layouts.base')

@section('subject', 'New contact request')

@section('content')
    <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        A new contact request from the website form:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
        @if($senderName)
        <tr>
            <td style="padding:8px 0; font-size:13px; font-weight:600; color:#374151; width:80px; vertical-align:top;">Name</td>
            <td style="padding:8px 0; font-size:14px; color:#111827;">{{ $senderName }}</td>
        </tr>
        @endif
        <tr>
            <td style="padding:8px 0; font-size:13px; font-weight:600; color:#374151; width:80px; vertical-align:top;">Email</td>
            <td style="padding:8px 0; font-size:14px; color:#111827;">{{ $senderEmail }}</td>
        </tr>
    </table>

    <p style="margin:0 0 10px; font-size:13px; font-weight:600; color:#374151; text-transform:uppercase; letter-spacing:0.05em;">Message</p>
    <div style="background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; padding:16px 20px;">
        <p style="margin:0; font-size:14px; line-height:1.7; color:#374151; white-space:pre-wrap;">{{ $senderMessage }}</p>
    </div>
@endsection
