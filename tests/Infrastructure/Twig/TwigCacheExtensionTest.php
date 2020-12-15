<?php

namespace App\Tests\Infrastructure\Twig;

use App\Infrastructure\Twig\TwigCacheExtension;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\CacheItem;

class TwigCacheExtensionTest extends TestCase
{

    /**
     * @var MockObject|AdapterInterface $cache
     */
    private $cache;

    private TwigCacheExtension $extension;

    protected function setUp(): void
    {
        /** @var MockObject|AdapterInterface $cache */
        $this->cache = $this->getMockBuilder(AdapterInterface::class)->getMock();
        $this->extension = new TwigCacheExtension($this->cache);
    }

    public function testCacheKeyString(): void
    {
        $this->assertEquals('hello', $this->extension->getCacheKey('hello'));
    }

    public function testCacheKeyEntity(): void
    {
        $entity = new CacheEntityFaker();
        $this->assertEquals($entity->getId() . 'CacheEntityFaker' . $entity->getUpdatedAt()->getTimestamp(), $this->extension->getCacheKey($entity));
    }

    public function testSetCacheValue(): void
    {
        $item = new CacheItem();
        $this->cache->expects($this->any())->method('getItem')->with('demo')->willReturn($item);
        $this->extension->setCacheValue('demo', 'hello');
        $this->assertEquals('hello', $item->get());
    }

    public function testGetCacheValue(): void
    {
        $item = new CacheItem();
        $item->set('hello');
        $this->cache->expects($this->any())->method('getItem')->with('demo')->willReturn($item);
        $value = $this->extension->getCacheValue('demo');
        $this->assertEquals($item->get(), $value);
    }

    public function testGetCacheValueWithoutValue(): void
    {
        $item = new CacheItem();
        $this->cache->expects($this->any())->method('getItem')->with('demo')->willReturn($item);
        $value = $this->extension->getCacheValue('demo');
        $this->assertEquals(null, $value);
    }

}