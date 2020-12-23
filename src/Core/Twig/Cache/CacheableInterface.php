<?php

namespace App\Core\Twig\Cache;

interface CacheableInterface
{

    public function getUpdatedAt(): \DateTimeInterface;

    public function getId(): int;

}
