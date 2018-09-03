<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;
use Tests\AppBundle\BootTest;

class DefaultControllerTest extends BootTest
{
    public function testHomepageNoUser()
    {
        $client = static::createClient();
        $client->request(Request::METHOD_GET, '/');

        static::assertEquals(
            302,
            $client->getResponse()->getStatusCode()
        );
    }

        public function testHomepageWithUser()
    {
       $this->client->request(Request::METHOD_GET, '/');

        static::assertEquals(
            200,
            $this->client->getResponse()->getStatusCode()
        );
    }


}
