<?php

namespace App\Domain\Auth\Repository;

use App\Domain\Auth\Entity\LoginAttempt;
use App\Domain\Auth\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LoginAttemptRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LoginAttempt::class);
    }

    public function countRecent(User $user, int $minutes): int
    {
        return $this->createQueryBuilder('l')
            ->select('COUNT(l.id) as count')
            ->where('l.user = :user')
            ->andWhere('l.createdAt > :date')
            ->setParameter('user', $user)
            ->setParameter('date', new \DateTime("-{$minutes} minutes"))
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleScalarResult();
    }

}
