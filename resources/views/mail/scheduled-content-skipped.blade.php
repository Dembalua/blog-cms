@extends('mail.layouts.base')

@section('subject', 'Scheduled content skipped')

@section('accent', '#D97706')
@section('badge', 'Content skipped')

@section('content')
    <p style="margin:0 0 20px; font-size:15px; line-height:1.6;">
        The following scheduled content was not published because no reachable URL could be found:
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
        @foreach ($skipped as $content)
        <tr>
            <td style="padding: 10px 14px; border-bottom: 1px solid #EFE7FC; font-size:14px; vertical-align:top; width:36px; color:#6B7280;">
                #{{ $content->id }}
            </td>
            <td style="padding: 10px 14px; border-bottom: 1px solid #EFE7FC; font-size:14px; vertical-align:top;">
                {{ $content->title }}
            </td>
        </tr>
        @endforeach
    </table>

    <p style="margin:24px 0 0; font-size:14px; color:#374151; line-height:1.6;">
        Assign a category or check the path configuration in the CMS.
    </p>
@endsection
