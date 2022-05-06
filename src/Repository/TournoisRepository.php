<?php

namespace App\Repository;

use App\Entity\Tournois;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use http\QueryString;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;

/**
 * @method Tournois|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournois|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournois[]    findAll()
 * @method Tournois[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournoisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournois::class);
    }

      //  /**
     // * @return Tournois[] Returns an array of Tournois objects
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
    public function findOneBySomeField($value): ?Tournois
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * @return void
     */
    public function countbyjeux($id)
    {
        $query= $this->createQueryBuilder('t')
            ->join('t.idjeux','j')            
            ->addselect('COUNT(t) as count')
            ->where('t.idjeux=:idjeux')
            ->setParameter('idjeux',$id)

        ;
        return $query->getQuery()->getResult();
    }
    


    public function findByNom($txtq)
    {
        $entityManager=$this->getEntityManager();
        $query=$entityManager
        ->createQuery("SELECT t from app\Entity\Tournois t where t.titre like :txt")
        ->setParameter('txt','%'.$txtq.'%');
        return $query->getResult();
    }



    /**
     * @return void
     */
    public function countParticipants($id)
    {
        $entityManager=$this->getEntityManager();
        $query=$entityManager 
        ->createQuery("SELECT t from app\Entity\Tournois t where t.idtournois=p.IdTournois and p.IdTournois=IdTournois")
            ->setParameter('IdTournois',$id)
        ;
        return $query->getResult();
    }

}
