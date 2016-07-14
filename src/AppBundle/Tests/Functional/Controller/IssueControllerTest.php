<?php

namespace AppBundle\Tests\Functional\Controller;

class IssueControllerTest extends WebTestCaseAbstract
{
    public function testShowAction()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', '/issue/first-issue');

        $this->assertContains('First issue..', $crawler->filter('h3')->text());
        $this->assertContains('Activity', $crawler->filter('.row.block')->eq(0)->filter('h4')->text());
        $this->assertEquals(2, $crawler->filter('.row.block')->eq(0)->filter('tbody tr')->count());
    }
}
