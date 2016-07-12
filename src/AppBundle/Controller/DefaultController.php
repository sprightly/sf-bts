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
        $systemActivity = new \stdClass();
        $systemActivity->blockTitle = $this->get('translator')->trans('System activity');
        $systemActivity->entities = $this->getDoctrine()
            ->getRepository('AppBundle:IssueActivity')
            ->findAll();

        return $this->render(
            'AppBundle:default:index.html.twig',
            array(
                'systemActivity' => $systemActivity,
            )
        );
    }

    /**
     * @Route("/profile", name="private_profile")
     */
    public function profileAction()
    {
        $user = $this->getUser();
        return $this->render(
            'AppBundle:default:public_profile.html.twig',
            array(
                'username' => $user->getUsername()
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
        return $this->render(
            'AppBundle:default:public_profile.html.twig',
            array(
                'username' => $username
            )
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
            'AppBundle:security:login.html.twig',
            array(
                'last_username' => $lastUsername,
                'last_error' => $lastError,
            )
        );
    }
}
