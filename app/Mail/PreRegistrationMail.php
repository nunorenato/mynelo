<?php

namespace App\Mail;

use App\Enums\StatusEnum;
use App\Models\BoatRegistration;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PreRegistrationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ?Collection $previousOwners;

    public function __construct(public BoatRegistration $registration)
    {
        $this->previousOwners = $registration->boat->registrations()
            ->withTrashed()
            ->where('id', '<>', $registration->id)
            ->whereNotIn('status', [StatusEnum::CANCELED])
            ->get();
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('nelo.emails.internal_from'), config('nelo.emails.from_name')),
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
