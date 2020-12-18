<?php

namespace App\Infrastructure\Queue\Handler;

use App\Infrastructure\Queue\Message\ServiceMethodMessage;
use Psr\Container\ContainerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ServiceMethodMessageHandler implements MessageHandlerInterface, ServiceSubscriberInterface
{

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __invoke(ServiceMethodMessage $message): void
    {
        /** @var callable $callable */
        $callable = [
            $this->container->get($message->getService()),
            $message->getMethod()
        ];
        call_user_func_array($callable, $message->getParams());
    }

    public static function getSubscribedServices(): array
    {
        return [
            MailerInterface::class => MailerInterface::class
        ];
    }
}
