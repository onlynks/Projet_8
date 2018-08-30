<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class DefaultControllerTest extends WebTestCase
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
        $client = static::createClient(array(), array(
            'PHP_AUTH_USER' => 'a',
            'PHP_AUTH_PW'   => 'a',
        ));

        $client->request(Request::METHOD_GET, '/');

        static::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }


}
