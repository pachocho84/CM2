<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Tag;

class TagFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $tags = array(
    /*1*/    array('name-en' => 'pianist', 'name-fr' => 'pianiste', 'name-it' => 'pianista', 'user' => true, 'page' => false),
    /*2*/    array('name-en' => 'violinist', 'name-fr' => 'violiniste', 'name-it' => 'violinista', 'user' => true, 'page' => false),
    /*3*/    array('name-en' => 'cellist', 'name-fr' => 'violoncelliste', 'name-it' => 'violoncellista', 'user' => true, 'page' => false),
    /*4*/    array('name-en' => 'double-bassist', 'name-fr' => 'contrebassiste', 'name-it' => 'contrabbassista', 'user' => true, 'page' => false),
    /*5*/    array('name-en' => 'flautist', 'name-fr' => 'flûtiste', 'name-it' => 'flautista', 'user' => true, 'page' => false),
    /*6*/    array('name-en' => 'oboist', 'name-fr' => 'hautboïste', 'name-it' => 'oboista', 'user' => true, 'page' => false),
    /*7*/    array('name-en' => 'clarinettist', 'name-fr' => 'clarinettiste', 'name-it' => 'clarinettista', 'user' => true, 'page' => false),
    /*8*/    array('name-en' => 'trumpeter', 'name-fr' => 'trompettiste', 'name-it' => 'trombettista', 'user' => true, 'page' => false),
    /*9*/    array('name-en' => 'tenor', 'name-fr' => 'ténor', 'name-it' => 'tenore', 'user' => true, 'page' => false),
    /*10*/    array('name-en' => 'music critic', 'name-fr' => 'critique de musique', 'name-it' => 'critico musicale', 'user' => true, 'page' => false),
    /*11*/    array('name-en' => 'musicologist', 'name-fr' => 'musicologue', 'name-it' => 'musicologo', 'user' => true, 'page' => false),
    /*12*/    array('name-en' => 'composer', 'name-fr' => 'compositeur', 'name-it' => 'compositore', 'user' => true, 'page' => false),
    /*13*/    array('name-en' => 'director', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => true, 'page' => false),
    /*14*/    array('name-en' => 'cultural association', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*15*/    array('name-en' => 'theatre', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*16*/    array('name-en' => 'auditorium', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*17*/    array('name-en' => 'orchestra', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*18*/    array('name-en' => 'ensemble', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*19*/    array('name-en' => 'choir', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*20*/    array('name-en' => 'conservatory', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*21*/    array('name-en' => 'accademy', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*22*/    array('name-en' => 'school of music', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*23*/    array('name-en' => 'artistic agency', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*24*/    array('name-en' => 'event producer', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*25*/    array('name-en' => 'musical competition', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*26*/    array('name-en' => 'label', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*27*/    array('name-en' => 'publisher', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*28*/    array('name-en' => 'magazine', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*29*/    array('name-en' => 'website', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*30*/    array('name-en' => 'radio', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*31*/    array('name-en' => 'television', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*32*/    array('name-en' => 'music store', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*33*/    array('name-en' => 'brand', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*34*/    array('name-en' => 'lutist', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*35*/    array('name-en' => 'tuner', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*36*/    array('name-en' => 'repairman', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*37*/    array('name-en' => 'reharsal room', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
    /*38*/    array('name-en' => 'studio', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'user' => false, 'page' => true),
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
        for ($i = 1; $i < count($this->tags); $i++) {
            $tag = new Tag;
            $tag->setVisible(true)
                ->setIsUser($this->tags[$i - 1]['user'])
                ->setIsPage($this->tags[$i - 1]['page']);
            $tag->setName($this->tags[$i - 1]['name-en']);
            $tag->translate('fr')->setName($this->tags[$i - 1]['name-fr']);
            $tag->translate('it')->setName($this->tags[$i - 1]['name-it']);

            $manager->persist($tag);
            $tag->mergeNewTranslations();
            $manager->flush();

            $this->addReference('tag-'.$i, $tag);
        }
    }

    public function getOrder()
    {
        return 10;
    }
}