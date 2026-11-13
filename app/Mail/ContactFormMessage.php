<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ContactFormMessage extends Mailable
{
    use Queueable;

    public function __construct(
        public string $senderName,
        public string $senderEmail,
        public string $senderMessage,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('New contact request'));
    }

    public function content(): Content
    {
        return new Content(view: 'mail.contact-form-message');
    }
}
