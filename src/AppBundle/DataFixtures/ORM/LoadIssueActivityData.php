<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\IssueActivity;

class LoadIssueActivityData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $firstActivity = new IssueActivity();
        $firstActivity->setIssue($this->getReference('first-issue'));
        $firstActivity->setProject($this->getReference('first-project'));
        $firstActivity->setType(IssueActivity::TYPE_CREATED);
        $firstActivity->setUser($this->getReference('usual-user'));
        $firstActivity->setCreated(new \DateTime('2016-07-01 10:11:31'));
        $manager->persist($firstActivity);

        $secondActivity = new IssueActivity();
        $secondActivity->setIssue($this->getReference('first-issue'));
        $secondActivity->setProject($this->getReference('first-project'));
        $secondActivity->setType(IssueActivity::TYPE_COMMENT);
        $secondActivity->setUser($this->getReference('usual-user'));
        $secondActivity->setCreated(new \DateTime('2016-07-01 18:11:31'));
        $manager->persist($secondActivity);

        $manager->flush();
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return 3;
    }
}
