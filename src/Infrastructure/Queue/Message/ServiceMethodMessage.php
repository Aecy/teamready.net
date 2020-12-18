<?php

namespace App\Infrastructure\Queue\Message;

class ServiceMethodMessage
{

    private string $service;
    private string $method;
    private array $params;

    public function __construct(string $service, string $method, array $params = [])
    {
        $this->service = $service;
        $this->method = $method;
        $this->params = $params;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getParams(): array
    {
        return $this->params;
    }

}
