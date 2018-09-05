<?php

namespace Tests\AppBundle;

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

    protected function tearDown()
    {
        $usersCreated = $this->em->getRepository(User::class)->findByUsername('testUser');

        if($usersCreated) {
            foreach($usersCreated as $userCreated) {
                $this->em->remove($userCreated);
                $this->em->flush();
            }
        }
    }


}
