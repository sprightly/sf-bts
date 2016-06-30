<?php
namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Project;

class LoadProjectData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $firstProject = new Project();
        $firstProject->setLabel('First sample project');
        $firstProject->setSummary(
            'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed non sem volutpat, 
            faucibus elit id, dapibus ligula. Suspendisse vehicula quam non tincidunt ultricies. Vestibulum ante ipsum 
            primis in faucibus orci luctus et ultrices posuere cubilia Curae; In mollis nunc sed ante lacinia ultricies
        '
        );
        $manager->persist($firstProject);
        $manager->flush();
        $this->addReference('first-project', $firstProject);
    }

    /**
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
