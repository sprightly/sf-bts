<?php

namespace AppBundle\Tests\Unit\Security;

use AppBundle\Entity\Comment;
use AppBundle\Entity\User;
use AppBundle\Security\CommentVoter;

class CommentVoterTest extends \PHPUnit_Framework_TestCase
{
    public function testNotExistsUserDuringVote()
    {
        $comment = new Comment();
        $decisionManager = $this
            ->createMock('\Symfony\Component\Security\Core\Authorization\AccessDecisionManager');
        $commentVoter = new CommentVoter($decisionManager);
        $token = $this->createMock('\Symfony\Component\Security\Core\Authentication\Token\AbstractToken');
        $token->method('getUser')
            ->willReturn(null);
        $this->assertEquals(-1, $commentVoter->vote($token, $comment, array('edit')));
    }

    public function testAdminCanEdit()
    {
        $comment = new Comment();
        $admin = new User();
        $admin->setRoles(array('ROLE_ADMIN'));

        $decisionManager = $this
            ->createMock('\Symfony\Component\Security\Core\Authorization\AccessDecisionManager');
        $decisionManager->method('decide')
            ->willReturn(true);

        $token = $this->createMock('\Symfony\Component\Security\Core\Authentication\Token\AbstractToken');
        $token->method('getUser')
            ->willReturn($admin);

        $commentVoter = new CommentVoter($decisionManager);
        $this->assertEquals(1, $commentVoter->vote($token, $comment, array('edit')));
    }

    public function testNotAdminAndNotAuthorCantEdit()
    {
        $comment = new Comment();
        $author = new User();
        $author->setUsername('author');
        $comment->setAuthor($author);
        $user = new User();
        $user->setRoles(array('ROLE_USER'));

        $decisionManager = $this
            ->createMock('\Symfony\Component\Security\Core\Authorization\AccessDecisionManager');
        $decisionManager->method('decide')
            ->willReturn(false);

        $token = $this->createMock('\Symfony\Component\Security\Core\Authentication\Token\AbstractToken');
        $token->method('getUser')
            ->willReturn($user);

        $commentVoter = new CommentVoter($decisionManager);
        $this->assertEquals(-1, $commentVoter->vote($token, $comment, array('edit')));
    }
}
