<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class WebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{

    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        /** @var EntityManagerInterface $em */
        $em = self::$container->get(EntityManagerInterface::class);
        $this->em = $em;
        parent::setUp();
    }

    public function expectErrorAlert(): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter('alert-message[type="danger"], alert-message[type="error"]')->count());
    }

    public function expectSuccessAlert(): void
    {
        $this->assertEquals(1, $this->client->getCrawler()->filter('alert-message[type="success"]')->count());
    }

    public function expectFormErrors(?int $expectedErrors = null): void
    {
        if ($expectedErrors === null) {
            $this->assertTrue($this->client->getCrawler()->filter('.form-error')->count() > 0, 'Form errors missmatch');
        } else {
            $this->assertEquals($expectedErrors, $this->client->getCrawler()->filter('.form-error')->count(), 'Form errors missmatch');
        }
    }

}
