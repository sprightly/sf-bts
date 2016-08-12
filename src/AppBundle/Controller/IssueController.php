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
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class IssueController extends Controller
{
    /**
     * @Route("issue/{slug}", name="single_issue")
     * @Security("is_granted('view', issue.getProject())")
     * @param Issue $issue
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction(Issue $issue)
    {
        $context = array();
        $context['entity'] = $issue;
        $context['allowSubTaskAdding'] = false;
        $context['allowEditing'] = false;
        if ($this->isGranted('add_issue', $context['entity']->getProject())) {
            $context['allowEditing'] = true;
            if (Issue::TYPE_STORY == $context['entity']->getType()) {
                $context['allowSubTaskAdding'] = true;
            }
        }
        $context['commentForm'] = $this->createCommentForm($context['entity'])->createView();

        return $this->render(
            'AppBundle:issue:single.html.twig',
            $context
        );
    }

    /**
     * @Route("/issue/{slug}/edit", name="edit_issue")
     * @Security("is_granted('add_issue', issue.getProject())")
     * @param Issue $issue
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editIssueAction(Issue $issue, Request $request)
    {
        return $this->addOrEditIssue($request, $issue->getProject(), null, $issue);
    }

    /**
     * @Route("/project/{slug}/issue/add", name="add_issue")
     * @Security("is_granted('add_issue', project)")
     * @param Project $project
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addIssueAction(Project $project, Request $request)
    {
        return $this->addOrEditIssue($request, $project);
    }

    /**
     * @Route("/issue/{slug}/add-subtask", name="add_sub_issue")
     * @Security("is_granted('add_issue', issue.getProject())")
     * @param Issue $issue
     * @param Request $request
     * @return \stdClass
     */
    public function addSubIssueAction(Issue $issue, Request $request)
    {
        return $this->addOrEditIssue($request, $issue->getProject(), $issue);
    }

    /**
     * @Route("issue/{slug}/comment/add-or-edit", name="add_or_edit_comment")
     * @Security("is_granted('add_issue', issue.getProject())")
     * @param Request $request
     * @param Issue $issue
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function addOrEditCommentAction(Request $request, Issue $issue)
    {
        $comment = null;
        if (is_array($request->get('comment')) && !empty($request->get('comment')['id'])) {
            $comment = $this->findCommentById(
                $request->get('comment')['id']
            );
            $this->denyAccessUnlessGranted('edit', $comment);
        }
        $form = $this->createCommentForm($issue, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Comment $comment */
            if (!$comment) {
                $comment = $form->getData();
                $comment->setAuthor($this->getUser());
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute('single_issue', array('slug' => $issue->getSlug()));
        } else {
            $context = array();
            $context['entity'] = $issue;
            $context['commentForm'] = $form->createView();

            return $this->render(
                'AppBundle:issue:single.html.twig',
                $context
            );
        }
    }

    /**
     * @Route("/comment/{id}/delete", name="comment_delete")
     * @Security("is_granted('delete', comment)")
     * @param Comment $comment
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @internal param $comment_id
     */
    public function deleteCommentAction(Comment $comment)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comment);
        $em->flush();

        return $this->redirectToRoute(
            'single_issue',
            array(
                'slug' => $comment->getIssue()->getSlug()
            )
        );
    }

    /**
     * @param Issue $issue
     * @param null $comment
     * @return \Symfony\Component\Form\Form
     */
    private function createCommentForm($issue, $comment = null)
    {
        if (!$comment) {
            $comment = new Comment();
        }
        $form = $this->createForm(
            CommentType::class,
            $comment,
            array(
                'issue' => $issue->getId(),
                'action' => $this->generateUrl('add_or_edit_comment', array('slug' => $issue->getSlug())),
            )
        );

        return $form;
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

        if ($parentIssue instanceof Issue
            || Issue::TYPE_STORY == $issue->getType()
            || Issue::TYPE_SUBTASK == $issue->getType()
        ) {
            $options['hideTypeInput'] = true;
        }

        if ($parentIssue instanceof Issue) {
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

            if ($parentIssue instanceof Issue) {
                $issue->setType(Issue::TYPE_SUBTASK);
                $issue->setParent($parentIssue);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($issue);
            $em->flush();

            return $this->redirectToRoute('single_project', array('project_slug' => $project->getSlug()));
        }

        $context['form'] = $form->createView();

        return $this->render(
            'AppBundle:issue:add-or-edit.html.twig',
            $context
        );
    }

    /**
     * @param $comment_id
     * @return Comment
     */
    private function findCommentById($comment_id)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $comment = $this->getDoctrine()
            ->getRepository('AppBundle:Comment')
            ->findOneById($comment_id);

        if (!$comment instanceof Comment) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans("Comment doesn't exists")
            );
        }

        return $comment;
    }
}
