<?php

namespace App\Mail;

use App\Models\Boat;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;

class RepairMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public Boat $boat, public string $description, public array $images)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('nelo.emails.internal_from'), config('nelo.emails.from_name')),
            replyTo: [new Address($this->user->email, $this->user->name)],
            subject: 'Boat repair request from My Nelo',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.repair',
        );
    }

    public function attachments(): array
    {
        Arr::map($this->images, [Attachment::class, 'fromPath']);
        return $this->images;
    }
}
