<?php

namespace Tests\AppBundle;

use AppBundle\Entity\Task;
use AppBundle\Entity\User;
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

    protected function createRandomTask() {

        $task = new Task();
        $task->setTitle('taskTest');
        $task->setContent('taskContentTest');

        $this->em->persist($task);
        $this->em->flush();

        $lastTask = $this->em->getRepository(Task::class)->getLast();

        return $lastTask;
    }

    protected function tearDown()
    {
        $tasksCreated= $this->em->getRepository(Task::class)->findAll();

        foreach($tasksCreated as $task) {
            $this->em->remove($task);
            $this->em->flush();
        }

        $usersCreated = $this->em->getRepository(User::class)->findAll();

        foreach($usersCreated as $user) {
            $this->em->remove($user);
            $this->em->flush();
        }
    }


}
