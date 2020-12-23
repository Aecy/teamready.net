<?php

namespace App\Tests\Core\Twig;

use App\Core\Twig\Cache\CacheableInterface;

class CacheEntityFaker implements CacheableInterface
{

    public function getUpdatedAt(): \DateTimeInterface
    {
        return new \DateTime("@123456789");
    }

    public function getId(): int
    {
        return 14;
    }

}
