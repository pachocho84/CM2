<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Disc;
use CM\CMBundle\Entity\DiscTrack;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;

class DiscFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $discs = array(
        array('title' => 'Concerto per pianoforte e orchestra n.I in si bemolle minore',
            'authors' => 'Pyotr Ilyich Tchaikovsky',
            'interpreters' => 'Svjatolav Richter, pianoforte; Wiener Symphoniker; Herbert von Karajan, direttore',
            'label' => 'EMI',
            'year' => '1962-1-1',
            'discTracks' => array(
                array('composer' => 'Pyotr Ilyich Tchaikovsky',
                    'title' => 'Allegro non troppo e molto maestoso - Allegro con spirito',
                    'movement' => 'I',
                    'artists' => 'Svjatolav Richter, pianoforte; Wiener Symphoniker; Herbert von Karajan, direttore',
                    'duration' => '0:23:05'
                ),
                array('composer' => 'Pyotr Ilyich Tchaikovsky',
                    'title' => 'Andantino semplice - Prestissimo',
                    'movement' => 'II',
                    'artists' => 'Svjatolav Richter, pianoforte; Wiener Symphoniker; Herbert von Karajan, direttore',
                    'duration' => '0:7:11'
                ),
                array('composer' => 'Pyotr Ilyich Tchaikovsky',
                    'title' => 'Allegro con fuoco',
                    'movement' => 'III',
                    'artists' => 'Svjatolav Richter, pianoforte; Wiener Symphoniker; Herbert von Karajan, direttore',
                    'duration' => '0:7:25'
                ),
            ),
            'img' => 'xb7e2be2de282f6cd99a91874f2f5134dd76cdd8.jpg'
        ),
        array('title' => 'La Folia',
            'authors' => 'Corelli, Marais, Martín y Coll, Ortiz & Anónimos',
            'interpreters' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
            'label' => 'AliaVox',
            'year' => '1998-1-1',
            'discTracks' => array(
                array('composer' => 'Anonyme',
                    'title' => 'Folia: Rodrigo Martinez - 1490',
                    'movement' => 'I Folias Antiguas',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:5:10'
                ),
                array('composer' => 'Diego Ortiz',
                    'title' => 'Recercada Quarta sobre la Folia - 1553',
                    'movement' => 'I Folias Antiguas',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:1:31'
                ),
                array('composer' => 'Antonio de Cabezón',
                    'title' => 'Folia: Para quien crié yo cabellos - 1557',
                    'movement' => 'I Folias Antiguas',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:2:10'
                ),
                array('composer' => 'Diego Ortiz',
                    'title' => 'Recercada Ottava sobre la Folia - 1553',
                    'movement' => 'I Folias Antiguas',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:1:47'
                ),
                array('composer' => 'Juan Del Enzina',
                    'title' => 'Folia: Hoy comamos y bebamos - vers 1520',
                    'movement' => 'I Folias Antiguas',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:3:12'
                ),
                array('composer' => 'Antonio Martín y Coll',
                    'title' => 'Deferencias sobre las folias - 1706-09',
                    'movement' => 'II Deferencias Sobre Las Folias',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:10:48'
                ),
                array('composer' => 'Arcangelo Corelli',
                    'title' => 'Follias, op.5 - 1700',
                    'movement' => 'III Follias',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:11:08'
                ),
                array('composer' => 'Marin Marais',
                    'title' => 'Couplets de folies, second livre de Pièces de viole - 1701',
                    'movement' => 'IV Couplets De Folies',
                    'artists' => 'Jordi Savall, Rolf Lislevand, Michael Behringer, Arianna Savall, Bruno Cocset, Pedro Estevan, Adela Gonzalez-Campa',
                    'duration' => '0:18:07'
                ),
            ),
            'img' => 'q834dc0cc993aa4397bdb860cea2e4ac970a65f5.jpg'
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
        for ($i = 1; $i < 201; $i++) {
            $discNum = rand(0, count($this->discs) - 1);
            $disc = new Disc;
            $disc->setAuthors($this->discs[$discNum]['authors'])
                ->setInterpreters($this->discs[$discNum]['interpreters'])
                ->setLabel($this->discs[$discNum]['label'])
                ->setYear(new \DateTime($this->discs[$discNum]['year']));
            $disc->setTitle($this->discs[$discNum]['title']);

            $manager->persist($disc);

            $disc->mergeNewTranslations();
               
            for ($j = 0; $j < count($this->discs[$discNum]['discTracks']); $j++) {
                $discTrack = new DiscTrack;
                $discTrack->setNumber($j + 1)
                    ->setComposer($this->discs[$discNum]['discTracks'][$j]['composer'])
                    ->setTitle($this->discs[$discNum]['discTracks'][$j]['title'])
                    ->setMovement($this->discs[$discNum]['discTracks'][$j]['movement'])
                    ->setArtists($this->discs[$discNum]['discTracks'][$j]['artists'])
                    ->setDuration(new \DateTime($this->discs[$discNum]['discTracks'][$j]['duration']));

                if (rand(0, 5) == 0) {
                    $discTrack->setAudio('62c3410ef8cc13c3dc7aa40646f9e805.mpga');
                }

                $disc->addDiscTrack($discTrack);
            }
            
            $userNum = rand(1, 8);
            $user = $manager->merge($this->getReference('user-'.$userNum));

            if (rand(0, 4) > 0) {
                $image = new Image;
                $image->setImg($this->discs[$discNum]['img'])
                    ->setText('main image for disc "'.$disc->getTitle().'"')
                    ->setMain(true)
                    ->setUser($user);
                $disc->addImage($image);                
    
                for ($j = rand(1, 4); $j > 0; $j--) {
                    $image = new Image;
                    $image->setImg($this->discs[$discNum]['img'])
                        ->setText('image number '.$j.' for disc "'.$disc->getTitle().'"')
                        ->setMain(false)
                        ->setUser($user);
                    
                    $disc->addImage($image);
                }

            }

            $category = $manager->merge($this->getReference('disc_category-'.rand(1, 2)));
            $category->addEntity($disc);

            $post = $this->container->get('cm.post_center')->getNewPost($user, $user);

            $disc->addPost($post);
            
            $disc->addUser(
                $user,
                true, // admin
                EntityUser::STATUS_ACTIVE,
                true // notification
            );

            $numbers = range(1, 8);
            unset($numbers[$userNum - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 6); $j++) {
                $otherUser = $manager->merge($this->getReference('user-'.$numbers[$j]));

                $disc->addUser(
                    $otherUser,
                    !rand(0, 3), // admin
                    EntityUser::STATUS_PENDING,
                    true // notification
                );
            }

            $manager->persist($disc);
            
            if ($i % 10 == 9) {
                $manager->flush();
            }

            $this->addReference('disc-'.$i, $disc);
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 65;
    }
}