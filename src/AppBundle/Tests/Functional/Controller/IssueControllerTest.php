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

    public function testAddIssueAction()
    {
        $testIssueSummary = 'test add issue action';
        $testIssueDescription = '...';
        $testIssueType = 'task';
        $testIssueStatus = 'open';
        $testIssuePriority = 'critical';

        $this->logIn();
        $crawler = $this->client->request('GET', '/project/first-sample-project/issue/add');

        $form = $crawler->filter('#issue_submit')->form(
            array(
                'issue[summary]' => $testIssueSummary,
                'issue[description]' => $testIssueDescription,
                'issue[type]' => $testIssueType,
                'issue[status]' => $testIssueStatus,
                'issue[priority]' => $testIssuePriority,
            )
        );
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $addedIssueRow = $crawler->filter('table')->eq(1)->children()->filter('tr')->eq(5)->children();
        $this->assertContains(
            $testIssueSummary,
            $addedIssueRow->filter('td')->eq(1)->text()
        );
        $this->assertContains(
            $testIssuePriority,
            $addedIssueRow->filter('td')->eq(2)->text()
        );
        $this->assertContains(
            $testIssueType,
            $addedIssueRow->filter('td')->eq(3)->text()
        );
        $this->assertContains(
            $testIssueStatus,
            $addedIssueRow->filter('td')->eq(4)->text()
        );
    }

    public function testDeleteCommentAction()
    {
        $this->logIn();
        $crawler = $this->client->request('GET', '/issue/first-issue');
        $link = $crawler->selectLink('delete')->link();
        $this->client->click($link);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        $crawler = $this->client->followRedirect();

        $this->assertEquals(2, $crawler->filter('.panel')->count());
    }
}
