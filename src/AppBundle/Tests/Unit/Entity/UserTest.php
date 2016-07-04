<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserTest extends WebTestCase
{
    /**@var User $user */
    protected $user;

    public function setup()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        /** @noinspection PhpUndefinedMethodInspection */
        $this->user = $container->get('doctrine')
            ->getRepository('AppBundle:User')->findOneByUsername('admin');
    }

    public function testGetId()
    {
        $userId = $this->user->getId();
        $this->assertTrue(is_int($userId));
    }
}
