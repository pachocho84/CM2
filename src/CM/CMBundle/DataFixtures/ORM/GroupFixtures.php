<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Group;
use CM\CMBundle\Entity\GroupUser;

class GroupFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    public static function count()
    {
        return 20;
    }

	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
	    $this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
        for ($i = 1; $i <= GroupFixtures::count(); $i++) {
            $userNum = rand(1, UserFixtures::count());
            $user = $manager->merge($this->getReference('user-'.$userNum));
            $group = new Group;
            $group->setType(Group::TYPE_DUO)
                ->setName('Group '.$i)
                ->setCreator($user)
                ->setDescription('description '.$i)
                ->setImg('ff9398d3d47436e2b4f72874a2c766fd.jpeg')
                ->setVip(rand(0, 1));
            
            $manager->persist($group);

            $post = $this->container->get('cm.post_center')->getNewPost($user, $user);

            $group->addPost($post);
                        
            $userTags = array();
            for ($j = 1; $j < rand(1, 3); $j++) {
                $userTags[] = $manager->merge($this->getReference('user_tag-'.rand(1, 10)))->getId();
            }
            
            $group->addUser(
                $user,
                true, // admin
                GroupUser::STATUS_ACTIVE,
                rand(0, 2), // join event
                rand(0, 2), // join disc
                rand(0, 2), // join article
                rand(0, 1), // notification
                $userTags
            );

            $numbers = range(1, UserFixtures::count());
            unset($numbers[$userNum - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 5); $j++) {
                $otherUser = $manager->merge($this->getReference('user-'.$numbers[$j]));
                
                $userTags = array();
                for ($k = 1; $k < rand(1, 3); $k++) {
                    $userTags[] = $manager->merge($this->getReference('user_tag-'.rand(1, 10)))->getId();
                }

                $group->addUser(
                    $otherUser,
                    false, // admin
                    GroupUser::STATUS_PENDING,
                    rand(0, 2), // join event
                    rand(0, 2), // join disc
                    rand(0, 2), // join article
                    rand(0, 1), // notification
                    $userTags
                );
            }
            
            $this->addReference('group-'.$i, $group);
        }

        $manager->flush();
    }

	public function getOrder()
	{
        return 30;
    }
}