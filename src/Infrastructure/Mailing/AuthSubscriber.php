<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Password\Event\PasswordResetTokenCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

/**
 * todo: refactor this class with Mailer
 */
class AuthSubscriber implements EventSubscriberInterface
{

    private EmailFactory $factory;
    private MailerInterface $mailer;

    public function __construct(EmailFactory $factory, MailerInterface $mailer)
    {
        $this->factory = $factory;
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            PasswordResetTokenCreatedEvent::class => 'onPasswordReset'
        ];
    }

    public function onPasswordReset(PasswordResetTokenCreatedEvent $event): void
    {
        $email = $this->factory->render('mails/auth/password_reset.html.twig', [
            'token' => $event->getToken()->getToken(),
            'id' => $event->getUser()->getId(),
            'username' => $event->getUser()->getUsername()
        ])
            ->to($event->getUser()->getEmail())
            ->from('no-reply@teamready.net')
            ->priority(Email::PRIORITY_HIGH)
            ->subject("Reset password");
        $this->mailer->send($email);
    }

}
