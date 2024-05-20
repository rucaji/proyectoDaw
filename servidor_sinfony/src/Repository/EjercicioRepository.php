<?php

namespace App\Repository;

use App\Entity\Ejercicio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ejercicio>
 */
class EjercicioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ejercicio::class);
    }

    public function findEjerciciosFiltrados($autor, $dificultad, $tema)
    {
        $qb = $this->createQueryBuilder('e');

        if (!empty($autor)) {
            $qb->andWhere('e.autor = :autor')->setParameter('autor', $autor);
        }
        if (!empty($dificultad)) {
            $qb->andWhere('e.dificultad = :dificultad')->setParameter('dificultad', $dificultad);
        }
        if (!empty($tema)) {
            $qb->andWhere('e.tema = :tema')->setParameter('tema', $tema);
        }

        return $qb->getQuery()->getResult();
    }
    //    /**
    //     * @return Ejercicio[] Returns an array of Ejercicio objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('e.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Ejercicio
    //    {
    //        return $this->createQueryBuilder('e')
    //            ->andWhere('e.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
