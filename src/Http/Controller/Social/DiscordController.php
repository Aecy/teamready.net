<?php

namespace App\Http\Controller\Social;

use App\Domain\Auth\User;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\Provider\DiscordClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Wohali\OAuth2\Client\Provider\DiscordResourceOwner;

class DiscordController extends AbstractController
{

    /**
     * @Route("/discord/connect", name="oauth_discord")
     * @IsGranted("ROLE_USER")
     */
    public function connect(DiscordClient $client): RedirectResponse
    {
        return $client->redirect(['identify', 'email']);
    }

    /**
     * @Route("/discord/check", name="oauth_discord_check")
     * @IsGranted("ROLE_USER")
     */
    public function check(DiscordClient $client, EntityManagerInterface $em): RedirectResponse
    {
        /** @var DiscordResourceOwner $discordUser */
        $discordUser = $client->fetchUser();
        /** @var User $user */
        $user = $this->getUser();

        $user->setDiscordId($discordUser->getId());
        $em->flush();

        $this->addFlash('success', "Discord account successfully linked");
        return $this->redirectToRoute('user_edit');
    }

}
