<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::toMailUsing(function ($user, $url) {
            return (new MailMessage)
                ->subject('Aktywacja konta — '.config('app.name'))
                ->greeting('Cześć, '.$user->name.'!')
                ->line('Potwierdź swój adres e-mail, aby aktywować konto w '.config('app.name').'.')
                ->action('Aktywuj konto', $url)
                ->line('Link jest ważny przez 60 minut. Jeśli nie zakładałeś konta, zignoruj tę wiadomość.')
                ->salutation('Pozdrawiamy, '.config('app.name'));
        });
        ResetPassword::toMailUsing(function ($user, $token) {
            $url = route('password.reset', ['token' => $token, 'email' => $user->email]);

            return (new MailMessage)
                ->subject('Zmiana hasła — '.config('app.name'))
                ->greeting('Cześć, '.$user->name.'!')
                ->line('Otrzymaliśmy prośbę o zmianę hasła do Twojego konta.')
                ->action('Ustaw nowe hasło', $url)
                ->line('Link jest ważny przez '.config('auth.passwords.users.expire').' minut i działa tylko raz. Jeśli nie wysyłałeś prośby, zignoruj tę wiadomość.')
                ->salutation('Pozdrawiamy, '.config('app.name'));
        });
    }
}
