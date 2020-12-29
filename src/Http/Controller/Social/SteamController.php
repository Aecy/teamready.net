<?php

namespace App\Http\Controller\Social;

use App\Domain\Auth\User;
use App\Infrastructure\Social\SteamClient;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class SteamController extends AbstractController
{

    /**
     * @Route("/steam/connect", name="oauth_steam")
     * @IsGranted("ROLE_USER")
     */
    public function connect(SteamClient $client): RedirectResponse
    {
        return $client->redirect();
    }

    /**
     * @Route("/steam/check", name="oauth_steam_check")
     * @IsGranted("ROLE_USER")
     */
    public function check(SteamClient $client, EntityManagerInterface $em)
    {
        $steamUser = $client->fetchUser();
        /** @var User $user */
        $user = $this->getUser();

        $user->setSteamId($steamUser->getSteamId());
        $em->flush();

        $this->addFlash('success', "Steam account successfully linked");
        return $this->redirectToRoute('user_edit');
    }

}
