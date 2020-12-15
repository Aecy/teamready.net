<?php

namespace App\Infrastructure\Mailing;

use Symfony\Component\Mime\Email;
use Twig\Environment;

final class EmailFactory
{

    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function render(string $template, array $data = []): Email
    {
        return (new Email())
            ->html($this->twig->render($template, $data));
    }

}
