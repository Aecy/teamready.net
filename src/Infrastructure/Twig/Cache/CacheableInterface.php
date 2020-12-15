<?php

namespace App\Infrastructure\Twig\Cache;

interface CacheableInterface
{

    public function getUpdatedAt(): \DateTimeInterface;

    public function getId(): int;

}