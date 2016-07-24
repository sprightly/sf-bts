<?php

namespace AppBundle\Security;

use AppBundle\Entity\Project;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProjectVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const ADD_ISSUE = 'add_issue';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::ADD_ISSUE))) {
            return false;
        }

        if (!$subject instanceof Project) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Project $project */
        $project = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($project, $token);
            case self::EDIT:
                return $this->canEdit($token);
            case self::ADD_ISSUE:
                return $this->canAddIssue($project, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Project $project, TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        $user = $token->getUser();
        if ($project->isMember($user)) {
            return true;
        }

        return false;
    }

    private function canEdit(TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN', 'ROLE_MANAGER'))) {
            return true;
        }

        return false;
    }

    private function canAddIssue(Project $project, TokenInterface $token)
    {
        if ($this->canEdit($token)) {
            return true;
        }

        $user = $token->getUser();
        if ($this->decisionManager->decide($token, array('ROLE_OPERATOR'))
            && $project->isMember($user)
        ) {
            return true;
        }

        return false;
    }
}
