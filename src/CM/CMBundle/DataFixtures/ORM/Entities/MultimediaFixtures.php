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
    public static $urls = array(
        array('source' => 'https://youtu.be/yVpbFMhOAwE', 'user' => 1),
        array('source' => 'http://vimeo.com/57815442', 'user' => 1),
        array('source' => 'https://soundcloud.com/aleksander-vinter/sheep-heavy-metal', 'user' => 1),
    );

    public static function count()
    {
        return count(MultimediaFixtures::$discs);
    } 

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
        $multimedia->setTitle($info['info']['title'])
            ->setText($info['info']['description']);

        $manager->persist($multimedia);

        $page = null;
        $group = null;
        if (array_key_exists('page', MultimediaFixtures::$urls[$i])) {
            $page = $manager->merge($fixture->getReference('page-'.MultimediaFixtures::$urls[$i]['page']));
            $user = $page->getCreator();
        } elseif (array_key_exists('group', MultimediaFixtures::$urls[$i])) {
            $group = $manager->merge($fixture->getReference('page-'.MultimediaFixtures::$urls[$i]['group']));
            $user = $group->getCreator();
        }
        if (array_key_exists('user', MultimediaFixtures::$urls[$i])) {
            $user = $manager->merge($fixture->getReference('user-'.MultimediaFixtures::$urls[$i]['user']));
        }

        $post = $this->container->get('cm.post_center')->getNewPost($user, $user);
        $manager->persist($post);
        $post->setPage($page);
        $post->setGroup($group);

        $multimedia->setPost($post);
    }
}