<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\User;

class UserTest extends \PHPUnit_Framework_TestCase
{
    /**@var User $user */
    protected $user;

    public function setUp()
    {
        $this->user = new User;
        $this->user->setUsername('operator');
        $this->user->setPassword(password_hash('test', PASSWORD_BCRYPT));
        $this->user->setFullName('Operator Full Name');
        $this->user->setEmail('operator@example.com');
        $this->user->setTimezone('America/Yellowknife');
        $this->user->setRoles(array(User::USER_ROLE, User::OPERATOR_ROLE));
    }

    public function testGetId()
    {
        $userEmail = $this->user->getEmail();
        $this->assertRegExp('/^[^@[:space:]]+@[^@[:space:]]+$/', $userEmail);
    }
}
