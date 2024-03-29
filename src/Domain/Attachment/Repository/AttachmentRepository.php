<?php

namespace App\Domain\Attachment\Repository;

use App\Domain\Attachment\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Attachment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Attachment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Attachment[]    findAll()
 * @method Attachment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AttachmentRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function findByDate(): array
    {
        $rows = $this->createQueryBuilder('a')
            ->select('EXTRACT(MONTH FROM a.createdAt) as month, EXTRACT(YEAR FROM a.createdAt) as year, COUNT(a.id) as count')
            ->groupBy('month', 'year')
            ->orderBy('month', 'DESC')
            ->orderBy('year', 'DESC')
            ->getQuery()
            ->getResult();
        return array_map(fn(array $row) => [
            'path' => $row['year'] . '/' . str_pad($row['month'], 2, '0', STR_PAD_LEFT),
            'count' => $row['count']
        ], $rows);
    }

    public function findByPath(string $path): array
    {
        $partials = explode('/', $path);
        $start = new \DateTimeImmutable("${partials[0]}-{$partials[1]}-01");
        $end = $start->modify('+1 month -1 second');

        return $this->createQueryBuilder('a')
            ->where('a.createdAt BETWEEN :start AND :end')
            ->setParameters([
                'start' => $start,
                'end' => $end
            ])
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(50)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return array<Attachment>
     */
    public function findLatest()
    {
        return $this->createQueryBuilder('a')
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(25)
            ->getQuery()
            ->getResult();
    }

}
