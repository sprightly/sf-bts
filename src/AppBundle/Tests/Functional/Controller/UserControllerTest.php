<?php

namespace AppBundle\Tests\Functional\Controller;

class UserControllerTest extends WebTestCaseAbstract
{
    public function testLoginAction()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Log in', $crawler->filter('form button')->text());
    }

    public function testPublicProfileAction()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/profile/operator');

        $this->assertContains('Public Profile', $crawler->filter('head title')->text());
        $this->assertContains('Operator Full Name', $crawler->filter('h3')->text());

        $this->assertContains('Activity', $crawler->filter('.row.block')->eq(0)->filter('h4')->text());
        $this->assertEquals(2, $crawler->filter('.row.block')->eq(0)->filter('tbody tr')->count());

        $this->assertContains('Issues', $crawler->filter('.row.block')->eq(1)->filter('h4')->text());
        $this->assertContains('Nothing here yet..', $crawler->filter('.row.block')->eq(1)->filter('tbody td')->text());

        $this->assertContains('edit profile', $crawler->filter('.jumbotron > a')->eq(0)->text());
    }
}
