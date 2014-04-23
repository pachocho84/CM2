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
    /*1*/    array('name-en' => 'pianist', 'name-fr' => 'pianiste', 'name-it' => 'pianista', 'type' => Tag::TYPE_USER),
    /*2*/    array('name-en' => 'violinist', 'name-fr' => 'violiniste', 'name-it' => 'violinista', 'type' => Tag::TYPE_USER),
    /*3*/    array('name-en' => 'cellist', 'name-fr' => 'violoncelliste', 'name-it' => 'violoncellista', 'type' => Tag::TYPE_USER),
    /*4*/    array('name-en' => 'double-bassist', 'name-fr' => 'contrebassiste', 'name-it' => 'contrabbassista', 'type' => Tag::TYPE_USER),
    /*5*/    array('name-en' => 'flautist', 'name-fr' => 'flûtiste', 'name-it' => 'flautista', 'type' => Tag::TYPE_USER),
    /*6*/    array('name-en' => 'oboist', 'name-fr' => 'hautboïste', 'name-it' => 'oboista', 'type' => Tag::TYPE_USER),
    /*7*/    array('name-en' => 'clarinettist', 'name-fr' => 'clarinettiste', 'name-it' => 'clarinettista', 'type' => Tag::TYPE_USER),
    /*8*/    array('name-en' => 'trumpeter', 'name-fr' => 'trompettiste', 'name-it' => 'trombettista', 'type' => Tag::TYPE_USER),
    /*9*/    array('name-en' => 'tenor', 'name-fr' => 'ténor', 'name-it' => 'tenore', 'type' => Tag::TYPE_USER),
    /*10*/    array('name-en' => 'music critic', 'name-fr' => 'critique de musique', 'name-it' => 'critico musicale', 'type' => Tag::TYPE_USER),
    /*11*/    array('name-en' => 'musicologist', 'name-fr' => 'musicologue', 'name-it' => 'musicologo', 'type' => Tag::TYPE_USER),
    /*12*/    array('name-en' => 'composer', 'name-fr' => 'compositeur', 'name-it' => 'compositore', 'type' => Tag::TYPE_USER),
    /*13*/    array('name-en' => 'director', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_USER),
    /*14*/    array('name-en' => 'cultural association', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*15*/    array('name-en' => 'theatre', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*16*/    array('name-en' => 'auditorium', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*17*/    array('name-en' => 'orchestra', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*18*/    array('name-en' => 'ensemble', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*19*/    array('name-en' => 'choir', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*20*/    array('name-en' => 'conservatory', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*21*/    array('name-en' => 'accademy', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*22*/    array('name-en' => 'school of music', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*23*/    array('name-en' => 'artistic agency', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*24*/    array('name-en' => 'event producer', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*25*/    array('name-en' => 'musical competition', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*26*/    array('name-en' => 'label', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*27*/    array('name-en' => 'publisher', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*28*/    array('name-en' => 'magazine', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*29*/    array('name-en' => 'website', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*30*/    array('name-en' => 'radio', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*31*/    array('name-en' => 'television', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*32*/    array('name-en' => 'music store', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*33*/    array('name-en' => 'brand', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*34*/    array('name-en' => 'lutist', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*35*/    array('name-en' => 'tuner', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*36*/    array('name-en' => 'repairman', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*37*/    array('name-en' => 'reharsal room', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*38*/    array('name-en' => 'studio', 'name-fr' => 'directeur', 'name-it' => 'direttore', 'type' => Tag::TYPE_PAGE),
    /*39*/    array('name-en' => 'teacher', 'name-fr' => 'teacher', 'name-it' => 'insegnante', 'type' => Tag::TYPE_USER),
    /*40*/    array('name-en' => 'agent', 'name-fr' => 'agent', 'name-it' => 'agente', 'type' => Tag::TYPE_USER),
    /*41*/    array('name-en' => 'project manager', 'name-fr' => 'agent', 'name-it' => 'project manager', 'type' => Tag::TYPE_USER),
    /*42*/    array('name-en' => 'editor', 'name-fr' => 'agent', 'name-it' => 'redattore', 'type' => Tag::TYPE_USER),
    /*43*/    array('name-en' => 'label manager', 'name-fr' => 'agent', 'name-it' => 'label manager', 'type' => Tag::TYPE_USER),
    /*44*/    array('name-en' => 'owner', 'name-fr' => 'agent', 'name-it' => 'proprietario', 'type' => Tag::TYPE_USER),
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
        foreach ($this->tags as $i => $t) {
            $tag = new Tag;
            $tag->setVisible(true)
                ->setType($t['type']);
            $tag->setName($t['name-en']);
            $tag->translate('fr')->setName($t['name-fr']);
            $tag->translate('it')->setName($t['name-it']);

            $manager->persist($tag);
            $tag->mergeNewTranslations();
            $manager->flush();

            $this->addReference('tag-'.($i + 1), $tag);
        }
    }

    public function getOrder()
    {
        return 10;
    }
}