<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Page;

class PageFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	private $images = array('ff9398d3d47436e2b4f72874a2c766fd.jpeg', '00b7b971d96ce05797e6757e5a0a4232.jpeg');
	
	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
	    $this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
        for ($i = 1; $i < 21; $i++) {
            $userNum = rand(1, 5);
            $user = $manager->merge($this->getReference('user-'.$userNum));
            $page = new Page;
            $page->setType(Page::TYPE_ASSOCIATION)
                ->setName('Page '.$i)
                ->setCreator($user)
                ->setDescription('description '.$i)
                ->setWebsite('www.google.com')
                ->setImg('ff9398d3d47436e2b4f72874a2c766fd.jpeg')
                ->setVip(rand(0, 1));
            
            $manager->persist($page);
                        
            $userTags = array();
            for ($j = 1; $j < rand(1, 3); $j++) {
                $userTags[] = $manager->merge($this->getReference('user_tag-'.rand(1, 10)))->getId();
            }
                            
            $page->addPageUser(
                $user,
                true, // admin
                rand(0, 2), // join event
                rand(0, 2), // join disc
                rand(0, 2), // join article
                rand(0, 1), // notification
                $userTags
            );

            $numbers = range(1, 5);
            unset($numbers[$userNum - 1]);
            shuffle($numbers);
            for ($j = 0; $j < 4; $j++) {
                $otherUser = $manager->merge($this->getReference('user-'.$numbers[$j]));
                
                $userTags = array();
                for ($k = 1; $k < rand(1, 3); $k++) {
                    $userTags[] = $manager->merge($this->getReference('user_tag-'.rand(1, 10)))->getId();
                }

                $page->addPageUser(
                    $otherUser,
                    !rand(0, 3), // admin
                    rand(0, 2), // join event
                    rand(0, 2), // join disc
                    rand(0, 2), // join article
                    rand(0, 1), // notification
                    $userTags
                );
            }
            
			$this->addReference('page-'.$i, $page);
        }

        $manager->flush();
	}

	public function getOrder()
	{
        return 4;
    }
}