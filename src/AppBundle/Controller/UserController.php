<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use /** @noinspection PhpUnusedAliasInspection */
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserController extends Controller
{
    /**
     * @Route("/profile/{username}/edit", name="private_profile")
     * @param $username
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function profileAction($username, Request $request)
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneByUsername($username);

        $this->denyAccessUnlessGranted('edit', $user);

        $form = $this->createForm(
            'AppBundle\Form\Type\UserType',
            $user,
            array('current_user_admin' => $this->isGranted('ROLE_ADMIN'))
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $this->maybeUpdatePassword($user, $request);
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            if ($user === $this->getUser()) {
                $token = new UsernamePasswordToken(
                    $user,
                    null,
                    'main',
                    $user->getRoles()
                );
                $this->get('security.token_storage')->setToken($token);
            }

            return $this->redirect($request->getRequestUri());
        }

        return $this->render(
            'AppBundle:user:private_profile.html.twig',
            array(
                'form' => $form->createView(),
            )
        );
    }

    /**
     * @Route("/profile/{username}", name="public_profile")
     * @param $username
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
        $context['canEdit'] = $this->isGranted('edit', $context['user']);

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

    private function maybeUpdatePassword(User $user, Request $request)
    {
        if (is_array($request->request->get('user')) && !empty($request->request->get('user')['password'])) {
            $user->setPassword(
                password_hash($request->request->get('user')['password'], PASSWORD_BCRYPT)
            );
        }
    }
}
