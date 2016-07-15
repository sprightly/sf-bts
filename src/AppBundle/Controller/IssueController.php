<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Issue;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class IssueController extends Controller
{
    /**
     * @Route("issue/{issue_slug}", name="single_issue")
     * @param $issue_slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($issue_slug)
    {
        $context = array();
        $context['entity'] = $this->getCurrentIssue($issue_slug);
        $this->denyAccessUnlessGranted('view', $context['entity']->getProject());
        $context['activityBlock'] = $this->generateActivityBlock($context['entity']);
        $context['collaboratorsBlock'] = $this->generateCollaboratorsBlock($context['entity']);
        $this->maybePopulateStorySubtaskContext($context, $context['entity']);
        
        return $this->render(
            'AppBundle:issue:single.html.twig',
            $context
        );
    }

    private function generateActivityBlock($issue)
    {
        $activityBlock = new \stdClass();
        $activityBlock->columns = array(
            'date',
            'type',
            'user',
        );
        $activityBlock->blockTitle = $this->get('translator')->trans('Activity');
        /** @noinspection PhpUndefinedMethodInspection */
        $activityBlock->entities = $this->getDoctrine()
            ->getRepository('AppBundle:IssueActivity')
            ->findByIssue($issue);

        return $activityBlock;
    }

    private function generateCollaboratorsBlock($issue)
    {
        $collaboratorsBlock = new \stdClass();
        $collaboratorsBlock->blockTitle = $this->get('translator')->trans('Collaborators');
        /** @noinspection PhpUndefinedMethodInspection */
        $collaboratorsBlock->entities = $issue->getCollaborators();

        return $collaboratorsBlock;
    }

    private function getCurrentIssue($issue_slug)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Issue $issue */
        $issue = $this->getDoctrine()
            ->getRepository('AppBundle:Issue')
            ->findOneBySlug($issue_slug);

        return $issue;
    }

    /**
     * @param array $context
     * @param Issue $issue
     */
    private function maybePopulateStorySubtaskContext(&$context, $issue)
    {
        if ($issue::TYPE_SUBTASK == $issue->getType()) {
            $context['parentTask'] = $issue->getParent();
        } elseif ($issue::TYPE_STORY == $issue->getType()) {
            $context['subTasksBlock']['blockTitle'] = $this->get('translator')->trans('Sub-task(s)');
            /** @noinspection PhpUndefinedMethodInspection */
            $context['subTasksBlock']['entities'] = $this->getDoctrine()
                ->getRepository('AppBundle:Issue')
                ->findByParent($issue);
            $context['subTasksBlock']['columns'] = array(
                'update',
                'summary',
                'project',
                'priority',
                'status',
                'assignee',
                'reporter',
            );
        }
    }
}
