<?php

namespace CM\CMBundle\DataFixtures\ORM\Entities;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\DataFixtures\ORM;

class MultimediaFixtures
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(AbstractFixture $fixture, ObjectManager $manager ,$i, $info)
    {
        $multimedia = new Multimedia;
        $multimedia->setType($info['type']);
        $multimedia->setSource($info['source']);
        $multimedia->setTitle($info['info']->title)
            ->setText($info['info']->description);

        $manager->persist($multimedia);

        $userNum = rand(1, ORM\UserFixtures::countPeople());
        $user = $manager->merge($fixture->getReference('user-'.$userNum));

        $page = null;
        $group = null;
        $pageOrGroup = rand(0, 100);
        if ($pageOrGroup < 20) {
            $page = $manager->merge($fixture->getReference('page-'.rand(1, ORM\PageFixtures::countPages())));
        } elseif ($pageOrGroup < 40) {
            $group = $manager->merge($fixture->getReference('group-'.rand(1, ORM\GroupFixtures::countGroups())));
        }

        $post = $this->container->get('cm.post_center')->getNewPost($user, $user);
        $post->setPage($page);
        $post->setGroup($group);

        $multimedia->addPost($post);
    }
}