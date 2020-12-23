<?php

namespace App\Tests;

use App\Core\Helper\PathHelper;
use Fidry\AliceDataFixtures\LoaderInterface;

trait FixturesTrait
{

    public function loadFixtures(array $fixtures): array
    {
        $fixturePath = $this->getPath();
        $files = array_map(fn ($fixture) => PathHelper::join($fixturePath, $fixture.'.yaml'), $fixtures);
        /** @var LoaderInterface $loader */
        $loader = static::$container->get('fidry_alice_data_fixtures.loader.doctrine');
        return $loader->load($files);
    }

    public function getPath(): string
    {
        return __DIR__ . '/fixtures/';
    }

}
