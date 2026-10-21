<?php

namespace App\Services;

use App\Models\Order;
use App\Notifications\OrderConfirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class OrderMail
{
    public function send(Order $order): bool
    {
        return DB::transaction(function () use ($order) {
            $locked = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
            if ($locked->status === 'cancelled') {
                return false;
            }
            if ($locked->confirmation_sent_at) {
                return true;
            }
            try {
                Notification::route('mail', $locked->email)->notify(new OrderConfirmation($locked));
                $locked->forceFill(['confirmation_sent_at' => now()])->save();

                return true;
            } catch (TransportExceptionInterface $exception) {
                // Błąd poczty nie cofa zakupu; wysyłkę można ponowić później.
                report($exception);

                return false;
            }
        });
    }
}
