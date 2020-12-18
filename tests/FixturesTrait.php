<?php

namespace App\Tests;

trait FixturesTrait
{

    use \Liip\TestFixturesBundle\Test\FixturesTrait {
        loadFixtureFiles as liipLoadFixtureFiles;
    }

    public function loadFixtures(array $fixtures): array
    {
        return $this->liipLoadFixtureFiles(
            array_map(function ($fixture) {
                return __DIR__ . '/fixtures/' . $fixture . '.yaml';
            }, $fixtures)
        );
    }

}
