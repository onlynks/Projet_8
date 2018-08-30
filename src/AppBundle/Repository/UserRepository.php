<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
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