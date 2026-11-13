@extends('mail.layouts.base')

@section('subject', 'CMS Error')

@section('accent', '#DC2626')
@section('badge', 'Error')

@section('content')
    <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        An error was logged on your site:
    </p>

    <div style="background:#FEF2F2; border:1px solid #FECACA; border-radius:8px; padding:16px 20px; margin-bottom:24px;">
        <p style="margin:0; font-family:monospace; font-size:13px; line-height:1.7; color:#7F1D1D; word-break:break-word; white-space:pre-wrap;">{{ $errorMessage }}</p>
    </div>

    @if (!empty($context))
    <p style="margin:0 0 10px; font-size:13px; font-weight:600; color:#374151; text-transform:uppercase; letter-spacing:0.05em;">Context</p>
    <div style="background:#F9FAFB; border:1px solid #E5E7EB; border-radius:8px; padding:16px 20px;">
        <p style="margin:0; font-family:monospace; font-size:12px; line-height:1.7; color:#374151; word-break:break-word; white-space:pre-wrap;">{{ json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</p>
    </div>
    @endif
@endsection
