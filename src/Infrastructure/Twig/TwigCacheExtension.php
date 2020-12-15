<?php

namespace App\Infrastructure\Twig;

use App\Infrastructure\Twig\Cache\CacheableInterface;
use App\Infrastructure\Twig\Cache\CacheTokenParser;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

class TwigCacheExtension
{

    private AdapterInterface $cache;

    public function __construct(AdapterInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @return array<CacheTokenParser>
     */
    public function getTokenParsers(): array
    {
        return [
            new CacheTokenParser()
        ];
    }

    /**
     * @return CacheableInterface|string|null $item
     */
    public function getCacheKey($item): string
    {
        if (is_string($item)) {
            return $item;
        }
        if (!is_object($item)) {
            throw new \Exception("Cache: Impossible de sérialiser une variable qui n'est pas un object ou une chaine");
        }
        try {
            $updatedAt = $item->getUpdatedAt();
            $id = $item->getId();
            $className = get_class($item);
            $className = substr($className, strrpos($className, '\\') + 1);
            return $id . $className . $updatedAt->getTimestamp();
        } catch (\Error $e) {
            throw new \Exception("Cache: Impossible de sérialiser l'object pour le cache {$e->getMessage()}");
        }
    }

    public function getCacheValue($item): ?string
    {
        /** @var CacheItem $item */
        $item = $this->cache->getItem($this->getCacheKey($item));
        return $item->get();
    }

    public function setCacheValue($item, string $value): void
    {
        /** @var CacheItem $item */
        $item = $this->cache->getItem($this->getCacheKey($item));
        $item->set($value);
        $this->cache->save($item);
    }

}