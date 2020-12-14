<?php

namespace App\Domain\Auth;

use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\Repository\LoginAttemptRepository;
use Doctrine\ORM\EntityManagerInterface;

class LoginAttemptService
{

    const ATTEMPTS = 3;

    private LoginAttemptRepository $repository;
    private EntityManagerInterface $em;

    public function __construct(
        LoginAttemptRepository $repository,
        EntityManagerInterface $em
    )
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    public function addAttempt(User $user): void
    {
        //@TODO: envoyer un email après 3 essais
        $attempt = (new LoginAttempt())->setUser($user);
        $this->em->persist($attempt);
        $this->em->flush();
    }

    public function limitReached(User $user): bool
    {
        return $this->repository->countRecent($user, 30) >= self::ATTEMPTS;
    }

}
