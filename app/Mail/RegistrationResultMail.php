<?php

namespace App\Mail;

use App\Models\BoatRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RegistrationResultMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public BoatRegistration $registration)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('nelo.emails.external_from'), config('nelo.emails.from_name')),
            subject: 'Your boat registration has been ' . $this->registration->status->toString(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.registration-result',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
