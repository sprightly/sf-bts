<?php
namespace AppBundle\DataFixtures\ORM;

use AppBundle\Type\IssuePriorityType;
use AppBundle\Type\IssueResolutionType;
use AppBundle\Type\IssueStatusType;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $firstIssue = new Issue();
        $firstIssue->setSummary('First issue..');
        $firstIssue->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies 
            in quis tellus. Suspendisse vulputate, nisl efficitur faucibus posuere, augue lacus ornare est, nec 
            interdum mauris neque ut arcu. Nunc dolor sapien, elementum ac ultrices id, accumsan id sapien. 
            Duis sed suscipit nulla, in ullamcorper lectus. Pellentesque quis turpis ligula. 
        '
        );
        $firstIssue->setType(Issue::TYPE_TASK);
        $firstIssue->setStatus(IssueStatusType::OPEN);
        $firstIssue->setPriority(IssuePriorityType::MAJOR);
        $firstIssue->setUpdated(new \DateTime('now'));
        $firstIssue->setCreated(new \DateTime('now'));
        $firstIssue->setAssignee($this->getReference('usual-user'));
        $firstIssue->setReporter($this->getReference('operator-user'));
        $firstIssue->setProject($this->getReference('first-project'));
        $manager->persist($firstIssue);

        $storyIssue = new Issue();
        $storyIssue->setSummary('Story issue..');
        $storyIssue->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies 
            in quis tellus. Suspendisse vulputate, nisl efficitur faucibus posuere, augue lacus ornare est, nec 
            interdum mauris neque ut arcu. Nunc dolor sapien, elementum ac ultrices id, accumsan id sapien. 
            Duis sed suscipit nulla, in ullamcorper lectus. Pellentesque quis turpis ligula. 
        '
        );
        $storyIssue->setType(Issue::TYPE_STORY);
        $storyIssue->setStatus(IssueStatusType::OPEN);
        $storyIssue->setPriority(IssuePriorityType::MAJOR);
        $storyIssue->setUpdated(new \DateTime('now'));
        $storyIssue->setCreated(new \DateTime('now'));
        $storyIssue->setAssignee($this->getReference('usual-user'));
        $storyIssue->setReporter($this->getReference('operator-user'));
        $storyIssue->setProject($this->getReference('first-project'));
        $manager->persist($storyIssue);

        $openSubTask = new Issue();
        $openSubTask->setSummary('Open sub-task..');
        $openSubTask->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies 
            in quis tellus. Suspendisse vulputate, nisl efficitur faucibus posuere, augue lacus ornare est, nec 
            interdum mauris neque ut arcu. Nunc dolor sapien, elementum ac ultrices id, accumsan id sapien. 
            Duis sed suscipit nulla, in ullamcorper lectus. Pellentesque quis turpis ligula. 
        '
        );
        $openSubTask->setType(Issue::TYPE_SUBTASK);
        $openSubTask->setStatus(IssueStatusType::OPEN);
        $openSubTask->setPriority(IssuePriorityType::MAJOR);
        $openSubTask->setUpdated(new \DateTime('now'));
        $openSubTask->setCreated(new \DateTime('now'));
        $openSubTask->setAssignee($this->getReference('usual-user'));
        $openSubTask->setReporter($this->getReference('operator-user'));
        $openSubTask->setProject($this->getReference('first-project'));
        $openSubTask->setParent($storyIssue);
        $manager->persist($openSubTask);

        $closedSubTask = new Issue();
        $closedSubTask->setSummary('Open sub-task..');
        $closedSubTask->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies 
            in quis tellus. Suspendisse vulputate, nisl efficitur faucibus posuere, augue lacus ornare est, nec 
            interdum mauris neque ut arcu. Nunc dolor sapien, elementum ac ultrices id, accumsan id sapien. 
            Duis sed suscipit nulla, in ullamcorper lectus. Pellentesque quis turpis ligula. 
        '
        );
        $closedSubTask->setType(Issue::TYPE_SUBTASK);
        $closedSubTask->setStatus(IssueStatusType::CLOSED);
        $closedSubTask->setPriority(IssuePriorityType::MAJOR);
        $closedSubTask->setUpdated(new \DateTime('now'));
        $closedSubTask->setCreated(new \DateTime('now'));
        $closedSubTask->setAssignee($this->getReference('usual-user'));
        $closedSubTask->setReporter($this->getReference('operator-user'));
        $closedSubTask->setProject($this->getReference('first-project'));
        $closedSubTask->setParent($storyIssue);
        $closedSubTask->setResolution(IssueResolutionType::FIXED);
        $manager->persist($closedSubTask);

        $manager->flush();

        $this->addReference('first-issue', $firstIssue);
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}
