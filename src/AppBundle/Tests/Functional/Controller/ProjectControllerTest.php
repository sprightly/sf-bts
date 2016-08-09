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

    public function testRestrictedShowAction()
    {
        $this->logIn();
        $this->client->request('GET', '/project/second-sample-project');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddAction()
    {
        $this->logIn('admin');
        $crawler = $this->client->request('GET', '/project/add');
        $testProjectLabel = 'test project label';
        $testProjectSummary = 'test project summary';

        $this->assertContains('Add Project', $crawler->filter('h3')->text());

        $form = $crawler->filter('#project_submit')->form(
            array(
                'project[label]' => $testProjectLabel,
                'project[summary]' => $testProjectSummary
            )
        );

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertContains($testProjectLabel, $crawler->filter('h3')->text());
        $this->assertContains($testProjectSummary, $crawler->filter('.jumbotron > p')->text());
    }
}
