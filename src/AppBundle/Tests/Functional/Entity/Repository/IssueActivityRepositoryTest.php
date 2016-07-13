<?php

namespace AppBundle\Tests\Functional\Entity\Repository;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class IssueActivityRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    private $user;

    protected function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;

        /** @noinspection PhpUndefinedMethodInspection */
        $this->user = $this->em
            ->getRepository('AppBundle:User')
            ->findOneByUsername('usual');
    }

    public function testFindAllVisibleForCurrentUser()
    {
        $visibleProjects = $this->em
            ->getRepository('AppBundle:IssueActivity')
            ->findAllVisibleForCurrentUser($this->user);
        ;

        $this->assertCount(2, $visibleProjects);
    }

    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null;
    }
}
