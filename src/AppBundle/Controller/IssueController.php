<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Issue;
use AppBundle\Entity\Project;
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
        $context['allowSubTaskAdding'] = false;
        if (Issue::TYPE_STORY == $context['entity']->getType()) {
            $context['allowSubTaskAdding'] = true;
        }

        $this->exitIfNotAllowedOrNotExistsIssue($context['entity']);

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

        if ($form->isSubmitted() && $form->isValid()) {
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

    /**
     * @Route("/issue/{issue_slug}/edit", name="edit_issue")
     */
    public function editIssueAction($issue_slug, Request $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Issue $issue */
        $issue = $this->getDoctrine()
            ->getRepository('AppBundle:Issue')
            ->findOneBySlug($issue_slug);

        return $this->addOrEditIssue($request, $issue->getProject(), null, $issue);
    }

    /**
     * @Route("/project/{project_slug}/issue/add", name="add_issue")
     * @param $project_slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addIssueAction($project_slug, Request $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBySlug($project_slug);

        if (!$project instanceof Project) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans("Project doesn't exists")
            );
        }

        $this->denyAccessUnlessGranted('add_issue', $project);

        return $this->addOrEditIssue($request, $project);
    }

    /**
     * @Route("/issue/{issue_slug}/add-subtask", name="add_sub_issue")
     * @param $issue_slug
     * @param Request $request
     * @return \stdClass
     */
    public function addSubIssueAction($issue_slug, Request $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Issue $issue */
        $issue = $this->getDoctrine()
            ->getRepository('AppBundle:Issue')
            ->findOneBySlug($issue_slug);

        $this->exitIfNotAllowedOrNotExistsIssue($issue);

        return $this->addOrEditIssue($request, $issue->getProject(), $issue);
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

    private function addOrEditIssue(Request $request, Project $project, $parentIssue = null, $currentIssue = null)
    {
        $context = array();
        $options = array();

        if ($currentIssue) {
            $issue = $currentIssue;
            $context['editAction'] = true;
        } else {
            $issue = new Issue();
        }

        if ($parentIssue) {
            $options['hideTypeInput'] = true;
            $context['parentIssue'] = $parentIssue;
        }
        $form = $this->createForm('AppBundle\Form\Type\IssueType', $issue, $options);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $issue->setProject($project);
            $issue->setSlug(
                preg_replace(
                    '/([^a-z0-9]+)/',
                    '-',
                    strtolower($issue->getSummary())
                )
            );

            if ($issue instanceof Issue) {
                $issue->setType(Issue::TYPE_SUBTASK);
                $issue->setParent($parentIssue);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            return $this->redirectToRoute('single_project', array('project_slug'=>$project->getSlug()));
        }

        $context['form'] = $form->createView();
        return $this->render(
            'AppBundle:issue:add-or-edit.html.twig',
            $context
        );
    }

    private function exitIfNotAllowedOrNotExistsIssue(Issue $issue)
    {
        if (!$issue instanceof Issue) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans("Issue doesn't exists")
            );
        }

        $this->denyAccessUnlessGranted('add_issue', $issue->getProject());
    }
}
