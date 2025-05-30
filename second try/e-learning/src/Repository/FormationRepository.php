<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function searchByTitle(string $query): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.title LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }

    public function searchAdvanced(?string $query, ?float $minPrice, ?float $maxPrice, ?string $sort = null): array
    {
        $qb = $this->createQueryBuilder('f');

        if ($query) {
            $qb->andWhere('f.title LIKE :q')->setParameter('q', "%$query%");
        }

        if ($minPrice !== null) {
            $qb->andWhere('f.price >= :min')->setParameter('min', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('f.price <= :max')->setParameter('max', $maxPrice);
        }

        if ($sort === 'price_asc') {
            $qb->orderBy('f.price', 'ASC');
        } elseif ($sort === 'price_desc') {
            $qb->orderBy('f.price', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }



//    /**
//     * @return Formation[] Returns an array of Formation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Formation
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
