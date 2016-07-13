<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setPassword(password_hash('test', PASSWORD_BCRYPT));
        $userAdmin->setFullName('Admin Full Name');
        $userAdmin->setEmail('admin@example.com');
        $userAdmin->setTimezone('America/Yellowknife');
        $userAdmin->setRoles(array(User::USER_ROLE, User::ADMIN_ROLE));
        $manager->persist($userAdmin);

        $userManager = new User();
        $userManager->setUsername('manager');
        $userManager->setPassword(password_hash('test', PASSWORD_BCRYPT));
        $userManager->setFullName('Manager Full Name');
        $userManager->setEmail('manager@example.com');
        $userManager->setTimezone('America/Yellowknife');
        $userManager->setRoles(array(User::USER_ROLE, User::MANAGER_ROLE));
        $manager->persist($userManager);

        $userOperator = new User();
        $userOperator->setUsername('operator');
        $userOperator->setPassword(password_hash('test', PASSWORD_BCRYPT));
        $userOperator->setFullName('Operator Full Name');
        $userOperator->setEmail('operator@example.com');
        $userOperator->setTimezone('America/Yellowknife');
        $userOperator->setRoles(array(User::USER_ROLE, User::OPERATOR_ROLE));
        $manager->persist($userOperator);

        $userUsual = new User();
        $userUsual->setUsername('usual');
        $userUsual->setPassword(password_hash('test', PASSWORD_BCRYPT));
        $userUsual->setFullName('Usual Full Name');
        $userUsual->setEmail('usual@example.com');
        $userUsual->setRoles(array(User::USER_ROLE));
        $userUsual->setTimezone('America/Yellowknife');
        $manager->persist($userUsual);

        $manager->flush();

        $this->addReference('admin-user', $userAdmin);
        $this->addReference('manager-user', $userManager);
        $this->addReference('operator-user', $userOperator);
        $this->addReference('usual-user', $userUsual);
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return 0;
    }
}
