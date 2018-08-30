<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
    public function getLast(){
        return $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('t')
            ->from('AppBundle:Task', 't')
            ->orderBy('t.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }
}