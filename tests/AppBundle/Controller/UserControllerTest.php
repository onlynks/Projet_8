<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;
use Symfony\Component\DomCrawler\Crawler;

class UserControllerTest extends WebTestCase
{
    private $em;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testList() {
        $em = $this->em;

        $client = static::createClient();

        $crawler = $client->request('GET','/users');

        $selections = $crawler->filter('tbody > tr > td:first-of-type')->each(function (Crawler $node) {
            return $node->text();
        });

        $users = $em->getRepository(User::class)->findAll();

        $usersName = [];

        foreach($users as $user){
            $usersName[] = $user->getUsername();
        }

        $this->assertSame($selections, $usersName);
    }

    public function testCreate() {

        $client = static::createClient();

        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form([
            'user[username]' => 'testMan',
            'user[password][first]' => 'testPass',
            'user[password][second]' => 'testPass',
            'user[email]' => 'testmail@gmail.com',
            'user[roles]' => 'ROLE_USER',
        ]);
        $client->submit($form);

        $client->followRedirect();

        $content  =  $client->getResponse()->getContent();

        $this->assertContains('Vous avez créé un utilisateur.', $content);

    }

    public function testUpdate() {

        $user = new User();
        $user->setUsername('testMan');
        $user->setPassword('test');
        $user->setEmail('test@gmail.com');
        $user->setRoles('ROLE_USER');
        $this->em->persist($user);
        $this->em->flush();

        $user = $this->em->getRepository(User::class)->findByUsername('testMan')[0];

        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');

        $userPassword = $user->getPassword().'!';

        $form = $crawler->selectButton('Modifier')->form([
            'user[password][first]' => $userPassword,
            'user[password][second]' => $userPassword,
        ]);
        $client->submit($form);

        $client->followRedirect();

        $updatedUser = $this->em->getRepository(user::class)->find($user);

        $this->assertSame($userPassword, $updatedUser->getPassword());
    }

    protected function tearDown()
    {
        $usersCreated = $this->em->getRepository(User::class)->findByUsername('testMan');

        if($usersCreated) {
            foreach($usersCreated as $userCreated) {
                $this->em->remove($userCreated);
                $this->em->flush();
            }
        }
    }

}
