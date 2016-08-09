<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends Controller
{
    /**
     * @Route("/project/add", name="add_project")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function addAction(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $project = new Project();
        $form = $this->createForm('AppBundle\Form\Type\ProjectType', $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $project->setSlug(
                preg_replace(
                    '/([^a-z0-9]+)/',
                    '-',
                    strtolower($project->getLabel())
                )
            );
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('single_project', array('project_slug'=>$project->getSlug()));
        }

        return $this->render(
            'AppBundle:project:add-or-edit.html.twig',
            array(
                'form' => $form->createView()
            )
        );
    }

    /**
     * @Route("project/{project_slug}", name="single_project")
     * @param $project_slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($project_slug)
    {
        $context = array();

        /** @noinspection PhpUndefinedMethodInspection */
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBySlug($project_slug);
        $context['entity'] = $project;

        $this->exitIfNotAllowedOrNotExists($project);

        $activityBlock = new \stdClass();
        $activityBlock->columns = array(
            'date',
            'issue',
            'type',
            'user',
        );
        $activityBlock->blockTitle = $this->get('translator')->trans('Activity');
        /** @noinspection PhpUndefinedMethodInspection */
        $activityBlock->entities = $this->getDoctrine()
            ->getRepository('AppBundle:IssueActivity')
            ->findByProject($project);
        $context['activityBlock'] = $activityBlock;

        $issuesBlock = new \stdClass();
        $issuesBlock->blockTitle = $this->get('translator')->trans('Issues');
        $issuesBlock->columns = array(
            'update',
            'summary',
            'priority',
            'type',
            'status',
            'assignee',
            'reporter',
        );
        /** @noinspection PhpUndefinedMethodInspection */
        $issuesBlock->entities = $this->getDoctrine()
            ->getRepository('AppBundle:Issue')
            ->findByProject($project);
        $context['issuesBlock'] = $issuesBlock;

        $context['allowEditing'] = false;
        if ($this->isGranted('edit', $project)) {
            $context['allowEditing'] = true;
        }
        $context['canAddIssue'] = $this->isGranted('add_issue', $project);

        return $this->render(
            'AppBundle:project:single.html.twig',
            $context
        );
    }

    /**
     * @Route("/project/{project_slug}/edit", name="edit_project")
     * @param $project_slug
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction($project_slug, Request $request)
    {
        $context = array();

        /** @noinspection PhpUndefinedMethodInspection */
        /** @var Project $project */
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBySlug($project_slug);
        $context['entity'] = $project;

        $this->exitIfNotAllowedOrNotExists($project);
        $this->denyAccessUnlessGranted('edit', $project);

        $form = $this->createForm('AppBundle\Form\Type\ProjectType', $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($project);
            $em->flush();

            return $this->redirectToRoute('single_project', array('project_slug'=>$project->getSlug()));
        }
        $context['form'] = $form->createView();
        $context['editAction'] = true;

        return $this->render(
            'AppBundle:project:add-or-edit.html.twig',
            $context
        );
    }

    private function exitIfNotAllowedOrNotExists($project)
    {
        if (!$project instanceof Project) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans("Project doesn't exists")
            );
        }

        $this->denyAccessUnlessGranted('view', $project);
    }
}
