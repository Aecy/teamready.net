<?php

namespace App\Http\Controller\Template;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PremiumStateController extends AbstractController
{

    public function state(): Response
    {
        $user = $this->getUser();

        return $this->render('partials/user_premium.html.twig', [
            'subscription' => false,
            'nextPayment' => new \DateTime('+1 month'),
            'active' => false,
            'premium' => false,
            'premiumEnd' => new \DateTime('last day of'),
            'user' => $user
        ]);
    }

}
