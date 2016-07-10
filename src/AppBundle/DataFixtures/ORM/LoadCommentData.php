<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $firstComment = new Comment();
        $firstComment->setBody('Hello world!');
        $firstComment->setCreated(new \DateTime('2016-07-01 18:11:31'));
        $firstComment->setAuthor($this->getReference('usual-user'));
        $firstComment->setIssue($this->getReference('first-issue'));
        $manager->persist($firstComment);

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
