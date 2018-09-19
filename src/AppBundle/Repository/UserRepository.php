<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getLast(){
        return $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('AppBundle:User', 'u')
            ->orderBy('u.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }
}