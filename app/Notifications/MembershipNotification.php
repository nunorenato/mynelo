<?php

namespace App\Notifications;

use App\Models\Membership;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MembershipNotification extends Notification
{
    public function __construct(private readonly Membership $membership)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->greeting('Congratulations!')
            ->line('Your membership status has been updated')
            ->line("You are now a {$this->membership->name} member")
            ->action('View your benefits', route('membership'))
            ->line('Thank you for using MyNelo!');
    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
