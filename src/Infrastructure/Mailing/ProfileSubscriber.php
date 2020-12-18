<?php

namespace App\Infrastructure\Mailing;

use App\Domain\Profile\Event\EmailVerificationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mime\Email;

class ProfileSubscriber implements EventSubscriberInterface
{

    private Mailer $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EmailVerificationEvent::class => 'onEmailChange'
        ];
    }

    public function onEmailChange(EmailVerificationEvent $event): void
    {
        $email = $this->mailer->create('mails/profile/email_confirmation.twig', [
            'username' => $event->emailVerification->getAuthor()->getUsername(),
            'token' => $event->emailVerification->getToken()
        ])
            ->to($event->emailVerification->getEmail())
            ->priority(Email::PRIORITY_HIGH)
            ->subject('Update email');
        $this->mailer->send($email);
    }
}
