<?php

namespace App\Http\Controller;

use App\Domain\Auth\Event\UserCreatedEvent;
use App\Domain\Auth\User;
use App\Domain\Password\TokenGeneratorService;
use App\Http\Form\RegistrationForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{

    /**
     * @Route("/register", name="register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        TokenGeneratorService $generatorService,
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher
    ): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('user_edit');
        }

        $user = new User();
        $form = $this->createForm(RegistrationForm::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $form->has('plainPassword') ? $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                ) : ''
            );
            $user->setCreatedAt(new \DateTime());
            $user->setConfirmationToken($generatorService->generate(60));
            $em->persist($user);
            $em->flush();
            $eventDispatcher->dispatch(new UserCreatedEvent($user));

            $this->addFlash('success', "We've send you an email of confirmation.");
        }

        return $this->render('registration/register.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/register/confirmation/{id}", name="register_confirm")
     */
    public function confirm(
        User $user,
        Request $request,
        EntityManagerInterface $em
    ): Response
    {
        $token = $request->get('token');
        if (empty($token) || $token !== $user->getConfirmationToken()) {
            $this->addFlash('error', "This token is invalid");
            return $this->redirectToRoute('register');
        }

        if ($user->getCreatedAt() < new \DateTime('-2 hours')) {
            $this->addFlash('error', "This token has expired");
            return $this->redirectToRoute('register');
        }

        $user->setConfirmationToken(null);
        $em->flush();
        $this->addFlash('success', "Your account is active, congratulations");

        return $this->redirectToRoute('auth_login');
    }

}
