<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmation extends Notification
{
    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('Potwierdzenie zamówienia #'.$this->order->id.' — '.config('app.name'))
            ->view('mail.order-confirmation', ['order' => $this->order->loadMissing('items')]);
    }
}
