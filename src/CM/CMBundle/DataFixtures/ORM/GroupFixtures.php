<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Group;

class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
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
            $user = $manager->merge($this->getReference('user-'.rand(1, 5)));
            $group = new Group;
            $group->setType(Group::TYPE_DUO)
                ->setName('Group '.$i)
                ->setCreator($user)
                ->setDescription('description '.$i)
                ->setImg('ff9398d3d47436e2b4f72874a2c766fd.jpeg')
                ->setVip(rand(0, 1));
            
            $manager->persist($group);

            $post = new Post;
            $post->setType(Post::TYPE_CREATION);

            $group->addPost($post);
            
            $manager->flush();
            
			$this->addReference('group-'.$i, $group);
        }
	}

	public function getOrder()
	{
        return 2;
    }
}