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
                    $info['info'] = json_decode(file_get_contents('http://www.youtube.com/oembed?format=json&url='.urlencode($url['source'])));
                    $info['type'] = Multimedia::TYPE_YOUTUBE;
                    $info['source'] = preg_replace('/^.*embed\/(.*)\?.*/', '$1', $info['info']->html);
                    $info['info'] = json_decode(file_get_contents('http://gdata.youtube.com/feeds/api/videos/'.$info['source'].'?v=2&alt=jsonc'))->data;
                    break;
                case 'vime':
                    $info['info'] = json_decode(file_get_contents('http://vimeo.com/api/oembed.json?url='.urlencode($url['source'])));
                    $info['type'] = Multimedia::TYPE_VIMEO;
                    $info['source'] = $info['info']->video_id;
                    break;
                case 'soun':
                    $info['info'] = json_decode(file_get_contents('http://soundcloud.com/oembed.json?url='.urlencode($url['source'])));
                    $info['type'] = Multimedia::TYPE_SOUNDCLOUD;
                    $info['source'] = preg_replace('/^.*tracks%2F(.*)&.*/', '$1', $info['info']->html);
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