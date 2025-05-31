<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    public function searchByTitle(string $query): array
    {
        return $this->createQueryBuilder('f')
            ->where('f.title LIKE :q')
            ->setParameter('q', '%' . $query . '%')
            ->getQuery()
            ->getResult();
    }


    public function searchAdvanced(
        ?string $query,
        ?float $minPrice,
        ?float $maxPrice,
        ?string $category = null,
        ?string $sort = null
    ): array {
        $qb = $this->createQueryBuilder('b');

        if ($query) {
            $qb->andWhere('b.title LIKE :q')
                ->setParameter('q', "%$query%");
        }

        if ($minPrice !== null) {
            $qb->andWhere('b.price >= :min')
                ->setParameter('min', $minPrice);
        }

        if ($maxPrice !== null) {
            $qb->andWhere('b.price <= :max')
                ->setParameter('max', $maxPrice);
        }

        if ($category) {
            $qb->andWhere('b.category = :cat')
                ->setParameter('cat', $category);
        }

        if ($sort === 'price_asc') {
            $qb->orderBy('b.price', 'ASC');
        } elseif ($sort === 'price_desc') {
            $qb->orderBy('b.price', 'DESC');
        }

        return $qb->getQuery()->getResult();
    }

    public function findAllCategories(): array
    {
        return $this->createQueryBuilder('b')
            ->select('DISTINCT b.category')
            ->where('b.category IS NOT NULL')
            ->orderBy('b.category', 'ASC')
            ->getQuery()
            ->getSingleColumnResult();
    }


//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
