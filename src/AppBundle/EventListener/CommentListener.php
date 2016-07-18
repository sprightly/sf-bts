<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Comment;
use AppBundle\Entity\IssueActivity;
use Doctrine\ORM\Event\LifecycleEventArgs;

class CommentListener
{
    public function postPersist(Comment $comment, LifecycleEventArgs $eventArgs)
    {
        $activity = new IssueActivity();
        $activity->setIssue($comment->getIssue());
        $activity->setProject($comment->getIssue()->getProject());
        $activity->setType(IssueActivity::TYPE_COMMENT);
        $activity->setUser($comment->getAuthor());

        $comment->getIssue()->addCollaborator($comment->getAuthor());

        $em = $eventArgs->getEntityManager();
        $em->persist($activity);
        $em->flush();
    }
}
