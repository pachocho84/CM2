<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\DataFixtures\ORM\Entities;

class EntitiesFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        $infoes = array();
        foreach (Entities\MultimediaFixtures::$urls as $url) {
            $info = array();
            switch (substr(preg_split('/(www|m)\./', parse_url($url['source'], PHP_URL_HOST), null, PREG_SPLIT_NO_EMPTY)[0], 0, 4)) {
                case 'yout':
                    $info['info'] = array('title' => 'How Linux is Built', 'description' => 'While Linux is running our phones, friend requests, tweets, financial trades, ATMs and more, most of us don\'t know how it\'s actually built. This short video takes you inside the process by which the largest collaborative development project in the history of computing is organized. Based on the annual report "Who Writes Linux," this is a powerful and inspiring story of how Linux has become a community-driven phenomenon. More information about Linux and The Linux Foundation can be found at http://www.linuxfoundation.org and http://www.linux.com');
                    $info['type'] = Multimedia::TYPE_YOUTUBE;
                    $info['source'] = 'yVpbFMhOAwE';
                    break;
                case 'vime':
                    $info['info'] = array('title' => 'Pig Box', 'description' => '《Pig Box》is a story about a shivering blue bird wants to get some heat from a sleepy porcupine. Directors: Ta-Wei Chao, Tsai-Chun Han Animation: Ta-Wei Chao Art Design: Tsai-Chun Han Music: Lily Chou Sound Design: Yin He Color: Shu-Yi Chiou 3D Effects: Yu-Tai Tsai Technical Support: Yeh-Chuan Yao');
                    $info['type'] = Multimedia::TYPE_VIMEO;
                    $info['source'] = '57815442';
                    break;
                case 'soun':
                    $info['info'] = array('title' => 'Sheep (heavy metal) by Aleksander Vinter', 'description' => 'A metal track I made a while ago. I spent some time programming the drums. ');
                    $info['type'] = Multimedia::TYPE_SOUNDCLOUD;
                    $info['source'] = '122834079';
                    break;
            }
            $infoes[] = $info;
        }

        $articleFixtures = new Entities\ArticleFixtures($this->container);
        $discFixtures = new Entities\DiscFixtures($this->container);
        $eventFixtures = new Entities\EventFixtures($this->container);
        $multimediaFixtures = new Entities\MultimediaFixtures($this->container);

        $entities = array();
        foreach (range(0, Entities\ArticleFixtures::count() - 1) as $i) {
            $entities[] = array('key' => $i, 'entity' => 'article');
        }
        foreach (range(0, Entities\DiscFixtures::count() - 1) as $i) {
            $entities[] = array('key' => $i, 'entity' => 'disc');
        }
        foreach (range(0, Entities\EventFixtures::count() - 1) as $i) {
            $entities[] = array('key' => $i, 'entity' => 'event');
        }
        foreach (range(0, Entities\ArticleFixtures::count() - 1) as $i) {
            $entities[] = array('key' => $i, 'entity' => 'multimedia');
        }
        shuffle($entities);

        foreach ($entities as $entity) {
            switch ($entity['entity']) {
                case 'article':
                    $articleFixtures->load($this, $manager, $entity['key']);
                    break;
                case 'disc':
                    $discFixtures->load($this, $manager, $entity['key']);
                    break;
                case 'event':
                    $eventFixtures->load($this, $manager, $entity['key'], $infoes);
                    break;
                case 'multimedia':
                    $multimediaFixtures->load($this, $manager, $entity['key'], $infoes[rand(0, count($infoes) - 1)]);
                    break;
            }
            
            $manager->flush();
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 60;
    }
}