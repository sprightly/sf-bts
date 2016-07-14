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
    /**
     * @param ObjectManager $manager
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function load(ObjectManager $manager)
    {
        $firstIssue = new Issue();
        $firstIssue->setSummary('First issue..');
        $firstIssue->setSlug('first-issue');
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
        $firstIssue->setUpdated(new \DateTime('2016-07-01 15:11:31'));
        $firstIssue->setCreated(new \DateTime('2016-07-01 10:11:31'));
        $firstIssue->setAssignee($this->getReference('usual-user'));
        $firstIssue->setReporter($this->getReference('operator-user'));
        $firstIssue->setProject($this->getReference('first-project'));
        /** @noinspection PhpParamsInspection */
        $firstIssue->addCollaborator($this->getReference('usual-user'));
        /** @noinspection PhpParamsInspection */
        $firstIssue->addCollaborator($this->getReference('operator-user'));
        $manager->persist($firstIssue);

        $storyIssue = new Issue();
        $storyIssue->setSummary('Story issue..');
        $storyIssue->setSlug('story-issue');
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
        $storyIssue->setUpdated(new \DateTime('2016-07-01 22:11:31'));
        $storyIssue->setCreated(new \DateTime('2016-07-01 19:11:31'));
        $storyIssue->setAssignee($this->getReference('usual-user'));
        $storyIssue->setReporter($this->getReference('operator-user'));
        $storyIssue->setProject($this->getReference('first-project'));
        /** @noinspection PhpParamsInspection */
        $storyIssue->addCollaborator($this->getReference('usual-user'));
        /** @noinspection PhpParamsInspection */
        $storyIssue->addCollaborator($this->getReference('operator-user'));
        $manager->persist($storyIssue);

        $openSubTask = new Issue();
        $openSubTask->setSummary('Open sub-task..');
        $openSubTask->setSlug('open-sub-task');
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
        $openSubTask->setUpdated(new \DateTime('2016-07-03 19:11:31'));
        $openSubTask->setCreated(new \DateTime('2016-07-01 18:15:31'));
        $openSubTask->setAssignee($this->getReference('usual-user'));
        $openSubTask->setReporter($this->getReference('operator-user'));
        $openSubTask->setProject($this->getReference('first-project'));
        /** @noinspection PhpParamsInspection */
        $openSubTask->addCollaborator($this->getReference('usual-user'));
        /** @noinspection PhpParamsInspection */
        $openSubTask->addCollaborator($this->getReference('operator-user'));
        $openSubTask->setParent($storyIssue);
        $manager->persist($openSubTask);

        $closedSubTask = new Issue();
        $closedSubTask->setSummary('Closed sub-task..');
        $closedSubTask->setSlug('closed-sub-task');
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
        $closedSubTask->setUpdated(new \DateTime('2016-07-05 10:11:31'));
        $closedSubTask->setCreated(new \DateTime('2016-07-04 12:11:31'));
        $closedSubTask->setAssignee($this->getReference('usual-user'));
        $closedSubTask->setReporter($this->getReference('operator-user'));
        $closedSubTask->setProject($this->getReference('first-project'));
        /** @noinspection PhpParamsInspection */
        $closedSubTask->addCollaborator($this->getReference('usual-user'));
        /** @noinspection PhpParamsInspection */
        $closedSubTask->addCollaborator($this->getReference('operator-user'));
        $closedSubTask->setParent($storyIssue);
        $closedSubTask->setResolution(IssueResolutionType::FIXED);
        $manager->persist($closedSubTask);

        $firstIssueInSecondProject = new Issue();
        $firstIssueInSecondProject->setSummary('First issue in second project..');
        $firstIssueInSecondProject->setSlug('first-issue-in-second-project');
        $firstIssueInSecondProject->setDescription(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies 
            in quis tellus. Suspendisse vulputate, nisl efficitur faucibus posuere, augue lacus ornare est, nec 
            interdum mauris neque ut arcu. Nunc dolor sapien, elementum ac ultrices id, accumsan id sapien. 
            Duis sed suscipit nulla, in ullamcorper lectus. Pellentesque quis turpis ligula. 
        '
        );
        $firstIssueInSecondProject->setType(Issue::TYPE_TASK);
        $firstIssueInSecondProject->setStatus(IssueStatusType::OPEN);
        $firstIssueInSecondProject->setPriority(IssuePriorityType::MAJOR);
        $firstIssueInSecondProject->setUpdated(new \DateTime('2016-07-01 15:11:31'));
        $firstIssueInSecondProject->setCreated(new \DateTime('2016-07-01 10:11:31'));
        $firstIssueInSecondProject->setAssignee($this->getReference('usual-user'));
        $firstIssueInSecondProject->setReporter($this->getReference('operator-user'));
        $firstIssueInSecondProject->setProject($this->getReference('second-project'));
        /** @noinspection PhpParamsInspection */
        $firstIssueInSecondProject->addCollaborator($this->getReference('usual-user'));
        /** @noinspection PhpParamsInspection */
        $firstIssueInSecondProject->addCollaborator($this->getReference('operator-user'));
        $manager->persist($firstIssueInSecondProject);
        
        $manager->flush();

        $this->addReference('first-issue', $firstIssue);
        $this->addReference('first-issue-in-second-project', $firstIssueInSecondProject);
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}
