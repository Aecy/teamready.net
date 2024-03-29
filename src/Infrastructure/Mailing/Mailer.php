<?php

namespace App\Infrastructure\Mailing;

use App\Infrastructure\Queue\EnqueueMethod;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class Mailer
{

    private Environment $twig;
    private EnqueueMethod $enqueue;

    public function __construct(Environment $twig, EnqueueMethod $enqueue)
    {
        $this->twig = $twig;
        $this->enqueue = $enqueue;
    }

    public function create(string $template, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from('no-reply@teamready.net')
            ->html($html)
            ->date( new \DateTime())
            ->text($text);
    }

    public function send(Email $email): void
    {
        $this->enqueue->enqueue(MailerInterface::class, 'send', [$email]);
    }

}
