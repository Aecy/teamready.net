<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;

trait FixturesTrait
{

    use \Liip\TestFixturesBundle\Test\FixturesTrait {
        loadFixtureFiles as liipLoadFixtureFiles;
    }

    /**
     * @param array<string> $fixtures
     * @return array<string,object>
     */
    public function loadFixtures(array $fixtures): array
    {
        return $this->liipLoadFixtureFiles(
            array_map(function ($fixture) {
                return __DIR__ . '/fixtures/' . $fixture . '.yaml';
            }, $fixtures)
        );
    }

}