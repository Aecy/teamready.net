<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Password\Data\PasswordResetConfirmData;
use App\Domain\Password\Data\PasswordResetRequestData;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Form\PasswordResetConfirmForm;
use App\Domain\Password\Form\PasswordResetRequestForm;
use App\Domain\Password\PasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{

    /**
     * @Route("/password/new", name="auth_password_reset")
     */
    public function reset(Request $request, PasswordService $resetService): Response
    {
        $error = null;
        $form = $this->createForm(PasswordResetRequestForm::class, new PasswordResetRequestData());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $resetService->resetPassword($form->getData());
                $this->addFlash('success', "Instruction for reset was send");
                return $this->redirectToRoute('auth_login');
            } catch (\Exception $e) {
                $error = $e;
            }
        }
        return $this->render('security/password_reset.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/password/new/{id}/{token}", name="auth_password_reset_confirm")
     */
    public function confirm(Request $request, User $user, PasswordResetToken $token, PasswordService $service): Response
    {
        if ($service->isExpired($token) || $token->getUser() !== $user) {
            $this->addFlash('error', "This token has expired");
            return $this->redirectToRoute('auth_login');
        }
        $error = null;
        $data = new PasswordResetConfirmData();
        $form = $this->createForm(PasswordResetConfirmForm::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $service->updatePassword($data->getPassword(), $user);
            $this->addFlash('success', "Your password has been updated");
            return $this->redirectToRoute('auth_login');
        }
        return $this->render('security/password_reset_confirm.html.twig', [
            'error' => $error,
            'form' => $form->createView()
        ]);
    }

}
