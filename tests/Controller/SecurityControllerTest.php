<?php

namespace App\Tests\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    use FixturesTrait;

    const RESET_PASSWORD_PATH = '/password/new';
    const RESET_PASSWORD_BUTTON = 'Send the instruction';
    const LOGIN_PATH = '/login';
    const LOGIN_PATH_BUTTON = 'Login';
    const FORGET_PASSWORD_BUTTON = 'Forget password ?';
    const RESET_PASSWORD_CONFIRM_BUTTON = 'Change password';

    public function testSeeLoginPage(): void
    {
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $this->assertEquals("Please sign in", $crawler->filter('h1')->text());
    }

    public function testBadPassword(): void
    {
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::LOGIN_PATH_BUTTON)->form();
        $form->setValues([
            'email' => 'john@doe.fr',
            'password' => '00000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testGoodPasswordWorks(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::LOGIN_PATH_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
            'password' => '0000'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testAttemptLimit(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $this->expectFormErrors(0);
        for ($i = 0; $i < 4; $i++) {
            $form = $crawler->selectButton(self::LOGIN_PATH_BUTTON)->form();
            $form->setValues([
                'email' => $users['user1']->getEmail(),
                'password' => '00000'
            ]);
            $this->client->submit($form);
            $this->assertResponseRedirects();
            $crawler = $this->client->followRedirect();
        }
        $this->assertStringContainsString('many', $crawler->filter('alert-message')->text());
    }
}
