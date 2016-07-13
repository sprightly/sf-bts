<?php

namespace AppBundle\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testLoginAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Log in', $crawler->filter('form button')->text());
    }
}
