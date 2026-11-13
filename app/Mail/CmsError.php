<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class CmsError extends Mailable
{
    use Queueable;

    public function __construct(
        public string $errorMessage,
        public array $context = [],
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'CMS Error');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.cms-error');
    }
}
