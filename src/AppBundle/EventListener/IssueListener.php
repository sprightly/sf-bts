<?php
namespace AppBundle\EventListener;

use AppBundle\Entity\Issue;
use AppBundle\Entity\IssueActivity;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IssueListener
{
    private $container = null;

    /**
     * IssueListener constructor.
     * @param Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    public function postPersist(Issue $issue, $eventArgs)
    {
        $this->createActivity($issue, IssueActivity::TYPE_CREATED, $eventArgs);
    }

    public function postUpdate(Issue $issue, LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();
        $changeSet = $uow->getEntityChangeSet($issue);
        if (!empty($changeSet) && isset($changeSet['status'])) {
            $this->createActivity($issue, IssueActivity::TYPE_STATUS_CHANGED, $eventArgs);
        }
    }

    private function createActivity(Issue $issue, $type, LifecycleEventArgs $eventArgs)
    {
        if ($this->container->get('security.token_storage')->getToken()) {
            /** @var TokenInterface $token */
            $token = $this->container->get('security.token_storage')->getToken();
            $author = $token->getUser();
        } else {
            $author = $issue->getReporter();
        }

        $activity = new IssueActivity();
        $activity->setIssue($issue);
        $activity->setProject($issue->getProject());
        $activity->setType($type);
        $activity->setUser($author);
        $issue->addCollaborator($author);
        $issue->getProject()->addMember($author);

        $em = $eventArgs->getEntityManager();
        $em->persist($activity);
        $em->flush();
    }
}
