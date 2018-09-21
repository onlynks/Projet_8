<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
use AppBundle\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class Boot extends WebTestCase
{
    protected $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPassword('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setEmail('admin@gmail.com');

        $this->em->persist($admin);

        $user = new User();
        $user->setUsername('user');
        $user->setPassword('user');
        $user->setRoles(['ROLE_USER']);
        $user->setEmail('user@gmail.com');

        $this->em->persist($user);
        $this->em->flush();
    }

    protected static function logAsUser() {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'user',
            'PHP_AUTH_PW'   => 'user',
        ));
    }

    protected static function logAsAdmin() {
        return  static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    protected function createRandomTask($taskRepository, $user = null) {

        $task = new Task();
        $task->setTitle('taskTest');
        $task->setContent('taskContentTest');
        if($user) {
            $task->setUser($user);
        }

        $this->em->persist($task);
        $this->em->flush();

        $lastTask = $taskRepository->getLast();

        return $lastTask;
    }

    protected function tearDown()
    {
        $usersCreated = $this->em->getRepository(User::class)->findAll();

        foreach($usersCreated as $user) {
            $this->em->remove($user);
        }

        $this->em->flush();
    }



}
