<?php

namespace AppBundle\Tests\Functional\Controller;

class DefaultControllerTest extends WebTestCaseAbstract
{
    public function testIndexAction()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/');

        $this->assertContains('Main', $crawler->filter('head title')->text());

        $this->assertContains('System activity', $crawler->filter('.row.block')->eq(0)->filter('h4')->text());
        $this->assertEquals(7, $crawler->filter('.row.block')->eq(0)->filter('tbody tr')->count());

        $this->assertContains('Issues', $crawler->filter('.row.block')->eq(1)->filter('h4')->text());
        $this->assertEquals(3, $crawler->filter('.row.block')->eq(1)->filter('tbody tr')->count());
    }
}
