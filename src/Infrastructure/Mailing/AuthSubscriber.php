<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Auth\Event\UserCreatedEvent;
use App\Domain\Password\Event\PasswordResetTokenCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;

class AuthSubscriber implements EventSubscriberInterface
{

    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordReset',
            UserCreatedEvent::class => 'onRegister'
        ];
    }

    public function onPasswordReset(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->mailer->create('mails/auth/password_reset.html.twig', [
            'token' => $event->getToken()->getToken(),
            'id' => $event->getUser()->getId(),
            'username' => $event->getUser()->getUsername()
        ])
            ->to($event->getUser()->getEmail())
            ->from('no-reply@teamready.net')
            ->priority(Email::PRIORITY_HIGH)
            ->subject("teamready | Reset password");
        $this->mailer->send($email);
    }

    public function onRegister(UserCreatedEvent $event): void
    {
        $email = $this->mailer->create('mails/auth/register.html.twig', [
            'user' => $event->getUser()
        ])
            ->to($event->getUser()->getEmail())
            ->from('no-reply@teamready.net')
            ->subject("teamready | Confirm account");
        $this->mailer->send($email);
    }

}
