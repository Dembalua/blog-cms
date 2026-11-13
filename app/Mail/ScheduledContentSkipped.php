<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class ScheduledContentSkipped extends Mailable
{
    use Queueable;

    public function __construct(public Collection $skipped) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Scheduled content skipped — no URL');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.scheduled-content-skipped');
    }
}
