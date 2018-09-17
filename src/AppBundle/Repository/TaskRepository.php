<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

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