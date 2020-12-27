<?php

namespace App\Http\Controller;

use App\Domain\Auth\User;
use App\Domain\Profile\Dto\AvatarDto;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\ProfileService;
use App\Http\Form\UpdatePasswordForm;
use App\Http\Form\UpdateProfileForm;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @method getUser() User
 */
class UserController extends AbstractController
{

    private UserPasswordEncoderInterface $passwordEncoder;
    private EntityManagerInterface $em;
    private ProfileService $profileService;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        EntityManagerInterface $em,
        ProfileService $profileService
    )
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->em = $em;
        $this->profileService = $profileService;
    }

    /**
     * @Route("/profil/edit", name="user_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        [$formPassword, $response] = $this->createFormPassword($request);
        if ($response) {
            return $response;
        }
        [$formUpdate, $response] = $this->createFormUpdate($request);
        if ($response) {
            return $response;
        }
        return $this->render('profil/edit.html.twig', [
            'user' => $user,
            'form_password' => $formPassword->createView(),
            'form_update' => $formUpdate->createView(),
        ]);
    }

    public function avatar(
        Request $request,
        EntityManagerInterface $em,
        ValidatorInterface $validator,
        ProfileService $service
    ): Response
    {
        $user = $this->getUser();
        $data = new AvatarDto($request->files->get('avatar'), $user);
        $errors = $validator->validate($data);
        if ($errors->count() > 0) {
            $this->addFlash('error', (string)$errors->get(0)->getMessage());
        } else {
            $service->updateAvatar($data);
            $em->flush();
            $this->addFlash('success', 'Avatar mis à jour avec succès');
        }

        return $this->redirectToRoute('user_edit');
    }

    private function createFormPassword(Request $request): array
    {
        $form = $this->createForm(UpdatePasswordForm::class);
        if ('password' !== $request->get('action')) {
            return [$form, null];
        }
        $user = $this->getUser();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user->setPassword($this->passwordEncoder->encodePassword($user, $data['password']));
            $this->em->flush();
            $this->addFlash('success', "Password updated");
            return [$form, $this->redirectToRoute('user_edit')];
        }
        return [$form, null];
    }

    private function createFormUpdate(Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(UpdateProfileForm::class, new ProfileUpdateDto($user));
        if ('update' !== $request->get('action')) {
            return [$form, null];
        }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->profileService->updateProfile($data);
            $this->em->flush();
            if ($user->getEmail() !== $data->email) {
                $this->addFlash('success', "Your profile is updated, you need to confirm this action on your inbox");
            } else {
                $this->addFlash('success', "Your profile is updated");
            }
            return [$form, $this->redirectToRoute('user_edit')];
        }
        return [$form, null];
    }

}
