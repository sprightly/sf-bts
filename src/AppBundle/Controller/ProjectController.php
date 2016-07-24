<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Project;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ProjectController extends Controller
{
    /**
     * @Route("project/{project_slug}", name="single_project")
     * @param $project_slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showAction($project_slug)
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

        $this->denyAccessUnlessGranted('view', $project);

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

        return $this->render(
            'AppBundle:project:single.html.twig',
            array(
                'entity' => $project,
                'activityBlock' => $activityBlock,
                'issuesBlock' => $issuesBlock,
                'canAddIssue' => $this->isGranted('add_issue', $project),
                'project_slug' => $project_slug
            )
        );
    }
}
