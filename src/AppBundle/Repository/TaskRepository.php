<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Task;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class TaskRepository
{
    /**
     * @var EntityRepository
     */
    public $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(Task::class);
    }

    public function getLast(){
        return $qb = $this->repository->createQueryBuilder('t')
            ->orderBy('t.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }
}