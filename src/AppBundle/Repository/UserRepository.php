<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class UserRepository
{
    /**
     * @var EntityRepository
     */
    public $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->repository = $entityManager->getRepository(User::class);
    }

    public function getLast(){
        return $qb = $this->repository->createQueryBuilder('u')
            ->orderBy('u.id','DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult()[0];
    }
}