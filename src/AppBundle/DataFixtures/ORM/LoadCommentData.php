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
        $firstComment->setBody(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies 
            in quis tellus. Suspendisse vulputate, nisl efficitur faucibus posuere, augue lacus ornare est, nec 
            interdum mauris neque ut arcu. Nunc dolor sapien, elementum ac ultrices id, accumsan id sapien. 
            Duis sed suscipit nulla, in ullamcorper lectus. Pellentesque quis turpis ligula.'
        );
        $firstComment->setCreated(new \DateTime('2016-07-01 18:11:31'));
        $firstComment->setAuthor($this->getReference('usual-user'));
        $firstComment->setIssue($this->getReference('first-issue'));
        $manager->persist($firstComment);

        $firstComment = new Comment();
        $firstComment->setBody(
            'Aliquam convallis vitae neque et egestas. Fusce a sodales purus. Sed lorem nibh, euismod quis est ut, 
            tempus gravida quam. In nec lobortis ante. Sed quis metus orci. Nulla vitae velit feugiat, convallis 
            lectus sed, tincidunt ante. Nullam ullamcorper quam et justo ullamcorper malesuada. Etiam tempor, 
            augue quis faucibus malesuada, velit orci pharetra magna, ut convallis tellus dui at lacus. '
        );
        $firstComment->setCreated(new \DateTime('2016-07-09 18:18:31'));
        $firstComment->setAuthor($this->getReference('operator-user'));
        $firstComment->setIssue($this->getReference('first-issue'));
        $manager->persist($firstComment);

        $firstComment = new Comment();
        $firstComment->setBody(
            'Suspendisse sit amet quam erat. Donec fermentum velit elit. Nunc cursus magna eu velit mattis venenatis. 
            Praesent facilisis mauris lacus. Proin eget nulla in nisl accumsan pharetra. Duis eget magna quis ligula 
            luctus iaculis. Sed eros lectus, ultrices eget odio et, suscipit fermentum nisi. Duis feugiat feugiat 
            mattis. In vehicula, dui quis viverra vestibulum, nibh tellus imperdiet mauris, vitae consequat 
            quam neque in sem. '
        );
        $firstComment->setCreated(new \DateTime('2016-07-11 18:18:31'));
        $firstComment->setAuthor($this->getReference('operator-user'));
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
