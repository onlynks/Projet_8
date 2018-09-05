<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Component\DomCrawler\Crawler;
use Tests\AppBundle\Boot;

class UserControllerTest extends Boot
{
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
            'user[username]' => 'testUser',
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
        $user->setUsername('testUser');
        $user->setPassword('test');
        $user->setEmail('test@gmail.com');
        $user->setRoles('ROLE_USER');
        $this->em->persist($user);
        $this->em->flush();

        $user = $this->em->getRepository(User::class)->findByUsername('testUser')[0];

        $client = static::logAsAdmin();

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
        $usersCreated = $this->em->getRepository(User::class)->findByUsername('testUser');

        if($usersCreated) {
            foreach($usersCreated as $userCreated) {
                $this->em->remove($userCreated);
                $this->em->flush();
            }
        }
    }

}
