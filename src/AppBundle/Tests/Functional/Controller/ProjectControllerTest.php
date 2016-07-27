<?php

namespace AppBundle\Tests\Functional\Controller;

class ProjectControllerTest extends WebTestCaseAbstract
{
    public function testShowAction()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/project/first-sample-project');

        $this->assertContains('First sample project', $crawler->filter('h3')->text());
        
        $this->assertContains('Activity', $crawler->filter('.row.block')->eq(0)->filter('h4')->text());
        $this->assertEquals(7, $crawler->filter('.row.block')->eq(0)->filter('tbody tr')->count());
        
        $this->assertContains('Issues', $crawler->filter('.row.block')->eq(1)->filter('h4')->text());
        $this->assertEquals(4, $crawler->filter('.row.block')->eq(1)->filter('tbody tr')->count());
    }
}
