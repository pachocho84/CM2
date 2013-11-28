<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Entity\PageUser;

class PageFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $pages = array(
        array('name' => 'Sony Classical Italia',
            'type' => Page::TYPE_ASSOCIATION,
            'creator' => 6,
            'description' => 'Sony Classical in Italia',
            'website' => 'www.sony.it',
            'img' => '650e5c4e916d35d9c4dd12f070f8c39f07ef26c9.jpg',
            'vip' => false
        ),
        array('name' => 'Società del Quartetto di Milano',
            'type' => Page::TYPE_ASSOCIATION,
            'creator' => 7,
            'description' => '1° settembre 1863 il manifesto di Tito Ricordi per una società con il compito di “incoraggiare i cultori della buona musica”.',
            'website' => 'www.quartettomilano.it',
            'img' => '22bd92a5ada9b1d8184c980ea1e226f9ee7c5130.jpg',
            'cover' => '2ce34d578aefc1b266fdd608b2c30090.jpeg',
            'background' => 'aaa8c7e3cae78c8861e2549942537a34.jpeg',
            'vip' => false
        ),
        array('name' => 'laVerdi',
            'type' => Page::TYPE_ASSOCIATION,
            'creator' => 8,
            'description' => 'Fondazione Orchestra Sinfonica e Coro Sinfonico di Milano Giuseppe Verdi',
            'website' => 'www.laverdi.org',
            'img' => '9834dc0cc993aa4397bdb860cea2e4ac970a65f5.jpg',
            'vip' => false
        ),
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
        foreach ($this->pages as $i => $p) {
            $user = $manager->merge($this->getReference('user-'.$p['creator']));
            $page = new Page;
            $page->setType($p['type'])
                ->setName($p['name'])
                ->setCreator($user)
                ->setDescription($p['description'])
                ->setWebsite($p['website'])
                ->setImg($p['img'])
                ->setVip($p['vip']);
            if (!is_null($p['cover'])) {
                $page->setCoverImg($p['cover']);
            }
            if (!is_null($p['background'])) {
                $page->setCoverImg($p['background']);
            }
            
            $manager->persist($page);
                        
            $userTags = array();
            for ($j = 1; $j < rand(1, 3); $j++) {
                $userTags[] = $manager->merge($this->getReference('user_tag-'.rand(1, 10)))->getId();
            }

            $post = $this->container->get('cm.post_center')->getNewPost($user, $user);

            $page->addPost($post);
            
            $page->addUser(
                $user,
                true, // admin
                PageUser::STATUS_ACTIVE,
                rand(0, 2), // join event
                rand(0, 2), // join disc
                rand(0, 2), // join article
                rand(0, 1), // notification
                $userTags
            );

            $numbers = range(1, 8);
            unset($numbers[$p['creator'] - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 7); $j++) {
                $otherUser = $manager->merge($this->getReference('user-'.$numbers[$j]));
                
                $userTags = array();
                for ($k = 1; $k < rand(1, 3); $k++) {
                    $userTags[] = $manager->merge($this->getReference('user_tag-'.rand(1, 10)))->getId();
                }

                $page->addUser(
                    $otherUser,
                    false, // admin
                    PageUser::STATUS_PENDING,
                    rand(0, 2), // join event
                    rand(0, 2), // join disc
                    rand(0, 2), // join article
                    rand(0, 1), // notification
                    $userTags
                );
            }
            
            $this->addReference('page-'.($i + 1), $page);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 3;
    }
}