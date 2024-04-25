<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $feedback, public int $rating, public ?string $why = null)
    {

    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('nelo.emails.internal_from'), config('nelo.emails.from_name')),
            replyTo: [new Address($this->user->email, $this->user->name)],
            subject: 'Feedback from My Nelo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
