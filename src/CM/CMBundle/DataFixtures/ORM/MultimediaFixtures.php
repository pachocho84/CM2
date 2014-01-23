<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\Post;

class MultimediaFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $urls = array(
        'https://youtu.be/yVpbFMhOAwE',
        'http://vimeo.com/57815442',
        'https://soundcloud.com/aleksander-vinter/sheep-heavy-metal',
        
    );

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach($this->urls as $url) {

            switch (substr(preg_split('/(www|m)\./', parse_url($url, PHP_URL_HOST), null, PREG_SPLIT_NO_EMPTY)[0], 0, 4)) {
                case 'yout':
                    $info = json_decode(file_get_contents('http://www.youtube.com/oembed?format=json&url='.urlencode($url)));
                    $type = Multimedia::TYPE_YOUTUBE;
                    $source = preg_replace('/^.*embed\/(.*)\?.*/', '$1', $info->html);
                    $info = json_decode(file_get_contents('http://gdata.youtube.com/feeds/api/videos/'.$source.'?v=2&alt=jsonc'))->data;
                    break;
                case 'vime':
                    $info = json_decode(file_get_contents('http://vimeo.com/api/oembed.json?url='.urlencode($url)));
                    $type = Multimedia::TYPE_VIMEO;
                    $source = $info->video_id;
                    break;
                case 'soun':
                    $info = json_decode(file_get_contents('http://soundcloud.com/oembed.json?url='.urlencode($url)));
                    $type = Multimedia::TYPE_SOUNDCLOUD;
                    $source = preg_replace('/^.*tracks%2F(.*)&.*/', '$1', $info->html);
                    break;
            }

            for ($i = 1; $i < 9; $i++) {
                $user = $manager->merge($this->getReference('user-'.$i));

                $multimedia = new Multimedia;
                $multimedia->setType($type);
                $multimedia->setSource($source);
                $multimedia->setTitle($info->title)
                    ->setText($info->description);

                $manager->persist($multimedia);

                $post = $this->container->get('cm.post_center')->getNewPost(
                    $user,
                    $user,
                    Post::TYPE_CREATION,
                    get_class($multimedia),
                    array(),
                    $multimedia
                );

                $multimedia->addPost($post);
            }    
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 100;
    }
}