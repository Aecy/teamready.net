<?php

namespace App\Http\Controller\Profile;

use App\Domain\Profile\EmailVerification;
use App\Domain\Profile\ProfileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EmailChangeController extends AbstractController
{

    /**
     * @Route("/email-confirm/{token}", name="user_email_confirm")
     */
    public function confirm(
        EmailVerification $emailVerification,
        ProfileService $profileService,
        EntityManagerInterface $em
    ): Response
    {
        if ($emailVerification->isExpired()) {
            $this->addFlash('error', "This confirmation has been expired");
        } else {
            $profileService->updateEmail($emailVerification);
            $em->flush();
            $this->addFlash('success', "Email successfully updated");
        }
        return $this->redirectToRoute('user_edit');
    }

}
