<?php

namespace AppBundle\Controller;

use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class UserController extends Controller
{
    /**
     * @Route("/profile", name="private_profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();

        return $this->render(
            'AppBundle:user:public_profile.html.twig',
            array(
                'username' => $user->getUsername(),
            )
        );
    }

    /**
     * @Route("/profile/{username}", name="public_profile")
     * @internal param $username
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function publicProfileAction($username)
    {
        $context = array();
        /** @noinspection PhpUndefinedMethodInspection */
        $context['user'] = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneByUsername($username);
        $context['activityBlock'] = $this->generateActivityBlock($context['user']);
        $context['issuesBlock'] = $this->generateIssueBlock($context['user']);
        $context['canEdit'] = $this->produceCanEdit($username);

        return $this->render(
            'AppBundle:user:public_profile.html.twig',
            $context
        );
    }

    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
        $authUtils = $this->get('security.authentication_utils');
        $lastError = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        return $this->render(
            'AppBundle:user:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'last_error' => $lastError,
            )
        );
    }

    private function generateActivityBlock($user)
    {
        $activityBlock = new \stdClass();
        $activityBlock->columns = array(
            'date',
            'project',
            'issue',
            'type',
        );
        $activityBlock->blockTitle = $this->get('translator')->trans('Activity');
        /** @noinspection PhpUndefinedMethodInspection */
        $activityBlock->entities = $this->getDoctrine()
            ->getRepository('AppBundle:IssueActivity')
            ->findByUser($user);

        return $activityBlock;
    }

    private function generateIssueBlock($user)
    {
        $issuesBlock = new \stdClass();
        $issuesBlock->blockTitle = $this->get('translator')->trans('Issues');
        /** @noinspection PhpUndefinedMethodInspection */
        $issuesBlock->entities = $this->getDoctrine()
            ->getRepository('AppBundle:Issue')
            ->findAllOpenIssueWhereUserAssignee($user);
        $issuesBlock->columns = array(
            'update',
            'summary',
            'project',
            'priority',
            'status',
            'assignee',
            'reporter',
        );

        return $issuesBlock;
    }

    private function produceCanEdit($username)
    {
        if ($this->getUser()->getUsername() == $username || $this->isGranted('ROLE_ADMIN')) {
            return true;
        } else {
            return false;
        }
    }
}
