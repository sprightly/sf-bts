<?php

namespace AppBundle\Tests\Unit\Security;

use AppBundle\Entity\User;
use AppBundle\Security\UserVoter;

class UserVoterTest extends \PHPUnit_Framework_TestCase
{
    public function testNotExistsUserDuringVote()
    {
        $user = new User();
        $decisionManager = $this
            ->createMock('\Symfony\Component\Security\Core\Authorization\AccessDecisionManager');
        $userVoter = new UserVoter($decisionManager);
        $token = $this->createMock('\Symfony\Component\Security\Core\Authentication\Token\AbstractToken');
        $token->method('getUser')
            ->willReturn(null);
        $this->assertEquals(-1, $userVoter->vote($token, $user, array('edit')));
    }

    public function testCantEdit()
    {
        $user = new User();
        $user->setUsername('user');
        $anotherUser = new User();
        $anotherUser->setUsername('another-user');
        $decisionManager = $this
            ->createMock('\Symfony\Component\Security\Core\Authorization\AccessDecisionManager');
        $token = $this->createMock('\Symfony\Component\Security\Core\Authentication\Token\AbstractToken');
        $token->method('getUser')
            ->willReturn($anotherUser);
        $userVoter = new UserVoter($decisionManager);
        $this->assertEquals(-1, $userVoter->vote($token, $user, array('edit')));
    }
}
