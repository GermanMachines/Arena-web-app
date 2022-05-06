<?php

namespace App\Repository;

use App\Entity\Jeux;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Jeux|null find($id, $lockMode = null, $lockVersion = null)
 * @method Jeux|null findOneBy(array $criteria, array $orderBy = null)
 * @method Jeux[]    findAll()
 * @method Jeux[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class JeuxRepository extends ServiceEntityRepository
{











    public function findByNom($txt)
    {
        $entityManager=$this->getEntityManager();
        $query=$entityManager
            ->createQuery("SELECT j from APP\Entity\Jeux j where j.nomjeux like :txt")
            ->setParameter('txt','%'.$txt.'%');
        return $query->getResult();
    }


    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Jeux::class);
    }

      //  /**
     // * @return Jeux[] Returns an array of Jeux objects
     //*/
    
    //public function findByExampleField($value)
   // {
    //    return $this->createQueryBuilder('e')
    //        ->andWhere('e.exampleField = :val')
     ///       ->setParameter('val', $value)
     //       ->orderBy('e.id', 'ASC')
       //     ->setMaxResults(10)
      //      ->getQuery()
      //      ->getResult()
     //   ;
    //}
  

    /*
    public function findOneBySomeField($value): ?Jeux
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('J')
            ->andWhere('J.idjeux LIKE :val')
            ->orWhere('J.nomjeux LIKE :val')
        
            ->setParameter('val','%'.$value.'%')
            ->orderBy('J.idjeux', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    







}
