<?php

namespace App\Domain\Password;

use App\Domain\Auth\Exception\UserNotFoundException;
use App\Domain\Auth\User;
use App\Domain\Auth\UserRepository;
use App\Domain\Password\Data\PasswordResetRequestData;
use App\Domain\Password\Entity\PasswordResetToken;
use App\Domain\Password\Event\PasswordRecoveredEvent;
use App\Domain\Password\Event\PasswordResetTokenCreatedEvent;
use App\Domain\Password\Exception\TokenExpiredException;
use App\Domain\Password\Repository\PasswordResetTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordService
{

    const EXPIRE_IN = 30;

    private UserRepository $userRepository;
    private PasswordResetTokenRepository $tokenRepository;
    private TokenGeneratorService $generator;
    private EntityManagerInterface $em;
    private EventDispatcherInterface $eventDispatcher;
    private UserPasswordEncoderInterface $encoder;

    public function __construct(
        UserRepository $userRepository,
        PasswordResetTokenRepository $tokenRepository,
        TokenGeneratorService $generator,
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher,
        UserPasswordEncoderInterface $encoder
    )
    {
        $this->userRepository = $userRepository;
        $this->tokenRepository = $tokenRepository;
        $this->generator = $generator;
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->encoder = $encoder;
    }

    public function resetPassword(PasswordResetRequestData $data): void
    {
        $user = $this->userRepository->findOneBy(['email' => $data->getEmail()]);
        if (empty($user)) {
            throw new UserNotFoundException();
        }

        $token = $this->tokenRepository->findOneBy(['user' => $user]);
        if ($token !== null && !$this->isExpired($token)) {
            throw new TokenExpiredException();
        }
        if ($token === null) {
            $token = new PasswordResetToken();
            $this->em->persist($token);
        }
        $token->setUser($user)
            ->setCreatedAt(new \DateTime())
            ->setToken($this->generator->generate());
        $this->em->flush();
        $this->eventDispatcher->dispatch(new PasswordResetTokenCreatedEvent($token));
    }

    public function isExpired(PasswordResetToken $token): bool
    {
        $expirationDate = new \DateTime('-' . self::EXPIRE_IN . ' minutes');
        return $token->getCreatedAt() < $expirationDate;
    }

    public function updatePassword(string $password, User $user): void
    {
        $user->setPassword($this->encoder->encodePassword($user, $password));
        $this->em->flush();
        $this->eventDispatcher->dispatch(new PasswordRecoveredEvent($user));
    }

}
