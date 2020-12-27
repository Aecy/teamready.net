<?php

namespace App\Domain\Profile;

use App\Domain\Password\TokenGeneratorService;
use App\Domain\Profile\Dto\AvatarDto;
use App\Domain\Profile\Dto\ProfileUpdateDto;
use App\Domain\Profile\Event\EmailVerificationEvent;
use App\Domain\Profile\Exception\TooManyEmailChangeException;
use App\Domain\Profile\Repository\EmailVerificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Intervention\Image\ImageManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProfileService
{

    private TokenGeneratorService $tokenGeneratorService;
    private EventDispatcherInterface $eventDispatcher;
    private EntityManagerInterface $em;
    private EmailVerificationRepository $emailVerificationRepository;

    public function __construct(
        TokenGeneratorService $tokenGeneratorService,
        EmailVerificationRepository $emailVerificationRepository,
        EventDispatcherInterface $eventDispatcher,
        EntityManagerInterface $em
    )
    {
        $this->tokenGeneratorService = $tokenGeneratorService;
        $this->eventDispatcher = $eventDispatcher;
        $this->em = $em;
        $this->emailVerificationRepository = $emailVerificationRepository;
    }

    public function updateAvatar(AvatarDto $data): void
    {
        if ($data->file->getRealPath() === false) {
            throw new \RuntimeException('Impossible de redimensionner un avatar non existant');
        }
        $manager = new ImageManager(['driver' => 'imagick']);
        $manager->make($data->file)->fit(110, 110)->save($data->file->getRealPath());
        $data->user->setAvatarFile($data->file);
        $data->user->setUpdatedAt(new \DateTime());
    }

    public function updateProfile(ProfileUpdateDto $data): void
    {
        $data->user->setCountry($data->country);
        $data->user->setMailNotification($data->mailNotification);
        if (true === $data->useSystemTheme) {
            $data->user->setTheme(null);
        } else {
            $data->user->setTheme($data->useDarkTheme ? 'dark' : 'light');
        }
        if ($data->email !== $data->user->getEmail()) {
            $lastRequest = $this->emailVerificationRepository->findLast($data->user);
            if ($lastRequest && $lastRequest->getCreatedAt() > new \DateTime('-1 week')) {
                throw new TooManyEmailChangeException($lastRequest);
            } else {
                if ($lastRequest) {
                    $this->em->remove($lastRequest);
                }
            }
            $emailVerification = (new EmailVerification())
                ->setEmail($data->email)
                ->setAuthor($data->user)
                ->setToken($this->tokenGeneratorService->generate())
                ->setCreatedAt(new \DateTime());
            $this->em->persist($emailVerification);
            $this->eventDispatcher->dispatch(new EmailVerificationEvent($emailVerification));
        }
    }

    public function updateEmail(EmailVerification $emailVerification): void
    {
        $emailVerification->getAuthor()->setEmail($emailVerification->getEmail());
        $this->em->remove($emailVerification);
    }

}
