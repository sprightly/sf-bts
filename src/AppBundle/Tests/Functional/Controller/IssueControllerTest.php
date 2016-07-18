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
        $this->assertEquals(4, $crawler->filter('.row.block')->eq(0)->filter('tbody tr')->count());
    }

    public function testNewCommentAction()
    {
        $testCommentBody = 'Test comment body...';
        $this->logIn();
        $crawler = $this->client->request('GET', '/issue/first-issue');

        $form = $crawler->filter('#comment_submit')->form(
            array(
                'comment[body]' => $testCommentBody,
            )
        );
        $this->client->submit($form);
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();
        $this->assertContains($testCommentBody, $crawler->filter('.panel-body')->last()->text());
    }
}
