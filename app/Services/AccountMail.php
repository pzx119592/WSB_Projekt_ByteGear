<?php

namespace App\Services;

use App\Models\User;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class AccountMail
{
    public function verify(User $user): bool
    {
        try {
            $user->sendEmailVerificationNotification();

            return true;
        } catch (TransportExceptionInterface $exception) {
            report($exception);

            return false;
        }
    }
}
