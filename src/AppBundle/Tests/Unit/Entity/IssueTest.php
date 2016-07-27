<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Issue;
use AppBundle\Validator\Constraints\ContainsProperSubTask;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Validator\ConstraintViolation;

class IssueTest extends WebTestCase
{
    /** @var Container $validator */
    private $container;

    /** @var  ContainsProperSubTask */
    private $constraintForProperSubTask;

    public function setUp()
    {
        static::$kernel = static::createKernel();
        static::$kernel->boot();
        $this->container = static::$kernel->getContainer();

        $this->constraintForProperSubTask = new ContainsProperSubTask();
    }

    public function testStoryAsSubTaskViolation()
    {
        $validator = $this->container->get('validator');

        $storyIssue = new Issue();
        $taskIssue = new Issue();
        $taskIssue->setType(Issue::TYPE_TASK);
        $storyIssue->setType(Issue::TYPE_STORY);
        $taskIssue->addChild($taskIssue);

        $errors = $validator->validate($taskIssue);
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            if (1 == $error->getCode()) {
                return;
            }
        }
        $this->fail('Failed testStoryAsSubTaskViolation!');
    }

    public function testChildrenInNotStoryViolation()
    {
        $validator = $this->container->get('validator');

        $storyIssue = new Issue();
        $taskIssue = new Issue();
        $taskIssue->setType(Issue::TYPE_TASK);
        $storyIssue->setType(Issue::TYPE_STORY);
        $storyIssue->setParent($taskIssue);

        $errors = $validator->validate($storyIssue);
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            if (1 == $error->getCode()) {
                return;
            }
        }
        $this->fail('Failed testStoryAsSubTaskViolation!');
    }

    public function testAccessToComments()
    {
        $issue = new Issue();
        $comment = new Comment();
        $secondComment = new Comment();

        $issue->addComment($comment);
        $this->assertEquals(1, count($issue->getComments()));

        $issue->addComment($secondComment);
        $this->assertEquals(2, count($issue->getComments()));

        $issue->removeComment($secondComment);
        $this->assertEquals(1, count($issue->getComments()));
    }
}
