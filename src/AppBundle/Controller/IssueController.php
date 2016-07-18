<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Issue;
use AppBundle\Form\Type\CommentType;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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

        $this->prepareBlocksContext($context);

        return $this->render(
            'AppBundle:issue:single.html.twig',
            $context
        );
    }

    /**
     * @Route("issue/{issue_slug}/comment/new", name="new_comment")
     * @param Request $request
     * @param $issue_slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newCommentAction(Request $request, $issue_slug)
    {
        $issue = $this->getCurrentIssue($issue_slug);

        $this->denyAccessUnlessGranted('view', $issue->getProject());

        $form = $this->createCommentForm($issue);
        $form->handleRequest($request);

        if ($this->isGranted('view', $issue->getProject()) && $form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setAuthor($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('single_issue', array('issue_slug' => $issue->getSlug()));
        } else {
            $context = array();
            $context['entity'] = $this->getCurrentIssue($issue_slug);
            $context['commentsBlock'] = new \stdClass();
            $context['commentsBlock']->form = $form->createView();
            $this->prepareBlocksContext($context);

            return $this->render(
                'AppBundle:issue:single.html.twig',
                $context
            );
        }
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

    /**
     * @param array $context
     * @return \stdClass
     * @internal param Issue $issue
     * @internal param \Symfony\Component\Form\Form $form
     */
    private function generateCommentsBlock(&$context)
    {
        if (!key_exists('commentsBlock', $context)) {
            $context['commentsBlock'] = new \stdClass();
        }
        $context['commentsBlock']->blockTitle = $this->get('translator')->trans('Comments');

        /** @noinspection PhpUndefinedMethodInspection */
        $context['commentsBlock']->entities = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findByIssue($context['entity']);

        if (!isset($context['commentsBlock']->form)) {
            $form = $this->createCommentForm($context['entity']);
            $context['commentsBlock']->form = $form->createView();
        }
    }

    /**
     * @param Issue $issue
     * @return \Symfony\Component\Form\Form
     */
    private function createCommentForm($issue)
    {
        $comment = new Comment();
        $form = $this->createForm(
            CommentType::class,
            $comment,
            array(
                'issue' => $issue->getId(),
                'action' => $this->generateUrl('new_comment', array('issue_slug' => $issue->getSlug())),
            )
        );

        return $form;
    }

    /**
     * @param array $context
     */
    private function prepareBlocksContext(&$context)
    {
        $context['activityBlock'] = $this->generateActivityBlock($context['entity']);
        $context['collaboratorsBlock'] = $this->generateCollaboratorsBlock($context['entity']);
        $this->generateCommentsBlock($context);
        $this->maybePopulateStorySubtaskContext($context, $context['entity']);
    }
}
