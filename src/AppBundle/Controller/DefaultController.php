<?php

namespace AppBundle\Controller;

use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="main_page")
     */
    public function indexAction()
    {
        $visibleForUserActivity = new \stdClass();
        $visibleForUserActivity->blockTitle = $this->get('translator')->trans('System activity');
        if ($this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
            $visibleForUserActivity->entities = $this->getDoctrine()
                ->getRepository('AppBundle:IssueActivity')
                ->findAll();
        } else {
            $visibleForUserActivity->entities = $this->getDoctrine()
                ->getRepository('AppBundle:IssueActivity')
                ->findAllVisibleForCurrentUser($this->getUser());
        }

        $usersIssues = new \stdClass();
        $usersIssues->blockTitle = $this->get('translator')->trans('Issues');
        if ($this->get('security.authorization_checker')->isGranted(array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
            $usersIssues->entities = $this->getDoctrine()
                ->getRepository('AppBundle:Issue')
                ->findAll();
        } else {
            $usersIssues->entities = $this->getDoctrine()
                ->getRepository('AppBundle:Issue')
                ->findAllOpenIssueWhereUserCollaborator($this->getUser());
        }

        return $this->render(
            'AppBundle:default:index.html.twig',
            array(
                'visibleForUserActivity' => $visibleForUserActivity,
                'usersIssues' => $usersIssues
            )
        );
    }
}
