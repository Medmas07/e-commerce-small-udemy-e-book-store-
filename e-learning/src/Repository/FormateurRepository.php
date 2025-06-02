<?php

namespace App\Repository;

use App\Entity\Formateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @extends ServiceEntityRepository<Formateur>
 */
class FormateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formateur::class);
    }

//    /**
//     * @return Formateur[] Returns an array of Formateur objects
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

//    public function findOneBySomeField($value): ?Formateur
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
// src/Repository/FormateurRepository.php

    public function calculateTotalRevenue(Formateur $formateur): float
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('COALESCE(SUM(pc.quantity * f.price), 0)')
            ->from('App\Entity\Commande', 'c')
            ->join('c.panier', 'p')
            ->join('p.produitChoisis', 'pc')
            ->join('pc.produit', 'f')
            ->join('App\Entity\Formation', 'fo', 'WITH', 'fo = f')
            ->where('c.statut = :paid')
            ->andWhere('fo.formateur = :formateur')
            ->setParameter('paid', 'paid')
            ->setParameter('formateur', $formateur);

        return (float) $qb->getQuery()->getSingleScalarResult();
    }


}
