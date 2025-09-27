<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use App\Models\Booking;

class BookingConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    protected $booking;
    public function __construct(Booking $booking) { $this->booking = $booking; }
    public function via($notifiable) { return ['mail']; }
    public function toMail($notifiable) {
        return (new MailMessage)
            ->subject('Booking Confirmed')
            ->line('Your booking #'.$this->booking->id.' is confirmed.')
            ->line('Event: '.$this->booking->ticket->event->title)
            ->line('Quantity: '.$this->booking->quantity)
            ->line('Thank you!');
    }
}
