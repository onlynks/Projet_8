<?php

namespace Tests\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Tests\AppBundle\Boot;

class DefaultControllerTest extends Boot
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
       $client = static::logAsAdmin();
       $client->request(Request::METHOD_GET, '/');

        static::assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }


}
