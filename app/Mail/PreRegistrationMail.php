<?php

namespace App\Mail;

use App\Models\BoatRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public BoatRegistration $registration)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: config('nelo.emails.internal_from'),
            subject: 'Boat registration initial validation',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.pre-registration',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
