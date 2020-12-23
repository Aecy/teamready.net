<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class PasswordControllerTest extends WebTestCase
{

    use FixturesTrait;

    const RESET_PASSWORD_PATH = '/password/new';
    const RESET_PASSWORD_BUTTON = 'Send the instruction';
    const LOGIN_PATH = '/login';
    const LOGIN_PATH_BUTTON = 'Login';
    const FORGET_PASSWORD_BUTTON = 'Forget password ?';
    const RESET_PASSWORD_CONFIRM_BUTTON = 'Change password';

    public function testResetPassword(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $crawler = $this->client->click($crawler->selectLink('Forget password ?')->link());
        $this->assertEquals('Forget password', $crawler->filter('h1')->text());
    }

    public function testResetPasswordBlockBadEmails(): void
    {
        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $crawler = $this->client->click($crawler->selectLink(self::FORGET_PASSWORD_BUTTON)->link());
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => 'hello',
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(1);
    }


    public function testResetPasswordShouldSendEmail(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);

        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $crawler = $this->client->click($crawler->selectLink(self::FORGET_PASSWORD_BUTTON)->link());
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        $this->assertEmailCount(1);
    }

    public function testResetPasswordShouldBlockRepeat(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);

        $crawler = $this->client->request('GET', self::LOGIN_PATH);
        $crawler = $this->client->click($crawler->selectLink(self::FORGET_PASSWORD_BUTTON)->link());
        $url = $crawler->getUri();

        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);

        $crawler = $this->client->request('GET', $url);
        $this->expectFormErrors(0);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->expectErrorAlert();
    }

    public function testResetPasswordShouldWorkWithOldPasswordAttempt(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['password-reset']);
        $crawler = $this->client->request('GET', self::RESET_PASSWORD_PATH);
        $form = $crawler->selectButton(self::RESET_PASSWORD_BUTTON)->form();
        $form->setValues([
            'email' => $users['user1']->getEmail(),
        ]);
        $this->client->submit($form);
        $this->assertEmailCount(1);
    }

    public function testResetPasswordConfirmChangePassword(): void
    {
        /** @var array<string,PasswordResetToken> $tokens */
        $tokens = $this->loadFixtures(['password-reset']);
        $token = $tokens['recent_password_token'];
        $this->client->request('GET', self::RESET_PASSWORD_PATH . "/{$token->getUser()->getId()}/{$token->getToken()}");
        $this->client->submitForm(self::RESET_PASSWORD_CONFIRM_BUTTON, [
            'password' => [
                'first' => "rgbhyuyghrhgbrrgrgrgrrgrg",
                'second' => "rgbhyuyghrhgbrrgrgrgrrgrg"
            ]
        ]);
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectSuccessAlert();
    }

    public function testResetPasswordConfirmExpired(): void
    {
        /** @var array<string,PasswordResetToken> $tokens */
        $tokens = $this->loadFixtures(['password-reset']);
        $token = $tokens['password_token'];
        $this->client->request('GET', self::RESET_PASSWORD_PATH . "/{$token->getUser()->getId()}/{$token->getToken()}");
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

}
