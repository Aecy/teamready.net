<?php

namespace App\Tests\Http\Controller;

use App\Domain\Auth\User;
use App\Tests\FixturesTrait;
use App\Tests\WebTestCase;

class RegisterControllerTest extends WebTestCase
{

    use FixturesTrait;

    private const SIGNUP_PATH = '/register';
    private const CONFIRMATION_PATH = '/register/confirmation/';
    private const SIGNUP_BUTTON = 'Register';

    public function testSEO(): void
    {
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $this->assertResponseStatusCodeSame(200);
        $this->assertEquals('Registration', $crawler->filter('h1')->text());
    }

    public function testRegisterSendMail(): void
    {
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $form->setValues([
            'registration_form' => [
                'username' => 'John Doe',
                'email' => 'john@doe.fr',
                'plainPassword' => [
                    'first' => 'mybigburger',
                    'second' => 'mybigburger'
                ]
            ]
        ]);
        $this->client->submit($form);
        $this->expectFormErrors(0);
        $this->assertEmailCount(1);
    }

    public function testRegisterExistingInformation(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $crawler = $this->client->request('GET', self::SIGNUP_PATH);
        $form = $crawler->selectButton(self::SIGNUP_BUTTON)->form();
        $formValues = [
            'registration_form' => [
                'username' => 'Jane Doe',
                'email' => $users['user1']->getEmail(),
                'plainPassword' => [
                    'first' => 'mybigburger',
                    'second' => 'mybigburger'
                ]
            ]
        ];
        $form->setValues($formValues);
        $this->client->submit($form);
        $this->expectFormErrors(1);
        $this->assertEmailCount(0);
        $formValues['registration_form']['username'] = $users['user1']->getUsername();
        $form->setValues($formValues);
        $this->client->submit($form);
        $this->expectFormErrors(2);
        $this->assertEmailCount(0);
    }

    public function testConfirmationTokenValid(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $user = $users['user_unconfirmed'];
        $user->setCreatedAt(new \DateTime());
        $this->em->flush();

        $this->client->request('GET', self::CONFIRMATION_PATH . $user->getId() . '?token=' . $user->getConfirmationToken());
        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->expectSuccessAlert();
    }

    public function testConfirmationTokenExpire(): void
    {
        /** @var array<string,User> $users */
        $users = $this->loadFixtures(['users']);
        $user = $users['user_unconfirmed'];
        $user->setCreatedAt(new \DateTime('-1 day'));
        $this->em->flush();

        $this->client->request('GET', self::CONFIRMATION_PATH . $user->getId() . '?token=' . $user->getConfirmationToken());
        $this->assertResponseRedirects(self::SIGNUP_PATH);
        $this->client->followRedirect();
        $this->expectErrorAlert();
    }

    public function testRedirectIfLogged(): void
    {
        /** @var User[] $data */
        $data = $this->loadFixtures(['users']);
        $this->login($data['user1']);
        $this->client->request('GET', self::SIGNUP_PATH);
        $this->assertResponseRedirects('/profil/edit');
    }
}
