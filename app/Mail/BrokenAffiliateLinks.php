<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Collection;

class BrokenAffiliateLinks extends Mailable
{
    use Queueable;

    public function __construct(public Collection $broken) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Broken affiliate links detected');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.broken-affiliate-links');
    }
}
