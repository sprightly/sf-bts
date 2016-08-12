<?php
namespace AppBundle\Twig;

use AppBundle\Entity\Issue;
use Symfony\Component\DependencyInjection\ContainerInterface;

class IssueBlocksExtension extends \Twig_Extension
{
    /** @var ContainerInterface */
    private $container;

    /**
     * DateTimeTimeZoneExtension constructor.
     * @param $container ContainerInterface
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('get_issue_blocks', array($this, 'prepareBlocksContext')),
        );
    }

    public function getName()
    {
        return 'get_issue_blocks';
    }

    public function prepareBlocksContext(Issue $issue)
    {
        $context = array();

        $context['activityBlock'] = $this->generateActivityBlock($issue);
        $context['collaboratorsBlock'] = $this->generateCollaboratorsBlock($issue);
        $context['commentsBlock'] = $this->generateCommentsBlock($issue);

        if ($issue::TYPE_SUBTASK == $issue->getType()) {
            $context['subTasksBlock'] = $this->getSubTasksBlock($issue);
        }

        return $context;
    }

    private function generateActivityBlock($issue)
    {
        $activityBlock = new \stdClass();
        $activityBlock->columns = array(
            'date',
            'type',
            'user',
        );
        $activityBlock->blockTitle = $this->container->get('translator')->trans('Activity');
        /** @noinspection PhpUndefinedMethodInspection */
        $activityBlock->entities = $this->container->get('doctrine')
            ->getRepository('AppBundle:IssueActivity')
            ->findByIssue($issue);

        return $activityBlock;
    }

    private function generateCollaboratorsBlock($issue)
    {
        $collaboratorsBlock = new \stdClass();
        $collaboratorsBlock->blockTitle = $this->container->get('translator')->trans('Collaborators');
        /** @noinspection PhpUndefinedMethodInspection */
        $collaboratorsBlock->entities = $issue->getCollaborators();

        return $collaboratorsBlock;
    }

    /**
     * @param Issue $issue
     */
    private function getSubTasksBlock($issue)
    {
        $block = array();
        $block['blockTitle'] = $this->container->get('translator')->trans('Sub-task(s)');
        /** @noinspection PhpUndefinedMethodInspection */
        $block['entities'] = $this->container->get('doctrine')
            ->getRepository('AppBundle:Issue')
            ->findByParent($issue);
        $block['columns'] = array(
            'update',
            'summary',
            'project',
            'priority',
            'status',
            'assignee',
            'reporter',
        );
    }


    /**
     * @return \stdClass
     * @internal param Issue $issue
     * @internal param \Symfony\Component\Form\Form $form
     */
    private function generateCommentsBlock($issue)
    {
        $block = new \stdClass();
        $block->blockTitle = $this->container->get('translator')->trans('Comments');

        /** @noinspection PhpUndefinedMethodInspection */
        $block->entities = $this->container->get('doctrine')
            ->getRepository('AppBundle:Comment')
            ->findByIssue($issue);

        return $block;
    }
}
