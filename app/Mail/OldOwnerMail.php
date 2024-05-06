<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class OldOwnerMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $password)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('nelo.emails.internal_from'), config('nelo.emails.from_name')),
            subject: 'Welcome to My Nelo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.old-owner',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
