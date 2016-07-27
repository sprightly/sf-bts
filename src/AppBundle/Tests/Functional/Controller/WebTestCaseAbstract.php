<?php

namespace AppBundle\Tests\Functional\Controller;

use AppBundle\DataFixtures\ORM\LoadCommentData;
use AppBundle\DataFixtures\ORM\LoadIssueData;
use AppBundle\DataFixtures\ORM\LoadProjectData;
use AppBundle\DataFixtures\ORM\LoadUserData;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class WebTestCaseAbstract extends WebTestCase
{
    /** @var Client */
    protected $client = null;

    /** @var EntityManager */
    protected $em = null;

    protected function setUp()
    {
        $this->client = static::createClient();
        $this->loadFixtures();
    }
    
    protected function logIn()
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->filter('form button')->form(
            array(
                '_username' => 'operator',
                '_password' => 'test'
            )
        );
        $this->client->submit($form);
    }

    protected function loadFixtures()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        $loader = new Loader();
        $loader->addFixture(new LoadUserData());
        $loader->addFixture(new LoadProjectData());
        $loader->addFixture(new LoadIssueData());
        $loader->addFixture(new LoadCommentData());

        $purger = new ORMPurger($this->em);
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }
}
