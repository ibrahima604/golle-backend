<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmail extends VerifyEmailBase
{
    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Bienvenue chez Golle ! Confirme ton adresse e-mail')
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ',')
            ->line('Merci de t’être inscrit sur Golle. Pour finaliser ton inscription, merci de vérifier ton adresse e-mail en cliquant sur le bouton ci-dessous.')
            ->action('Vérifier mon e-mail', $verificationUrl)
            ->line('Si tu n’as pas créé ce compte, tu peux ignorer ce message.')
            ->salutation('À bientôt, l’équipe Golle.');
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify', 
            Carbon::now()->addMinutes(60), 
            ['id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),]
        );
    }
}
