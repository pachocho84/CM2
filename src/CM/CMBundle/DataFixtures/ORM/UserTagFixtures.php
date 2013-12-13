<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\UserTag;

class UserTagFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $tags = array(
        array('name-en' => 'pianist', 'name-fr' => 'pianiste', 'name-it' => 'pianista'),
        array('name-en' => 'violinist', 'name-fr' => 'violiniste', 'name-it' => 'violinista'),
        array('name-en' => 'cellist', 'name-fr' => 'violoncelliste', 'name-it' => 'violoncellista'),
        array('name-en' => 'double-bassist', 'name-fr' => 'contrebassiste', 'name-it' => 'contrabbassista'),
        array('name-en' => 'flautist', 'name-fr' => 'flûtiste', 'name-it' => 'flautista'),
        array('name-en' => 'oboist', 'name-fr' => 'hautboïste', 'name-it' => 'oboista'),
        array('name-en' => 'clarinettist', 'name-fr' => 'clarinettiste', 'name-it' => 'clarinettista'),
        array('name-en' => 'trumpeter', 'name-fr' => 'trompettiste', 'name-it' => 'trombettista'),
        array('name-en' => 'tenor', 'name-fr' => 'ténor', 'name-it' => 'tenore'),
        array('name-en' => 'music critic', 'name-fr' => 'critique de musique', 'name-it' => 'critico musicale'),
        array('name-en' => 'musicologist', 'name-fr' => 'musicologue', 'name-it' => 'musicologo'),
        array('name-en' => 'composer', 'name-fr' => 'compositeur', 'name-it' => 'compositore'),
        array('name-en' => 'director', 'name-fr' => 'directeur', 'name-it' => 'direttore'),
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
		for ($i = 1; $i < 11; $i++) {
		    $userTag = new UserTag;
		    $userTag->setVisible(true)
                ->setIsUser(true)
                ->setIsGroup(true)
                ->setIsPage(true)
                ->setIsProtagonist(true);
            $userTag->setName($this->tags[$i - 1]['name-en']);
            $userTag->translate('fr')->setName($this->tags[$i - 1]['name-fr']);
            $userTag->translate('it')->setName($this->tags[$i - 1]['name-it']);

            $manager->persist($userTag);
            $userTag->mergeNewTranslations();
            $manager->flush();

            $this->addReference('user_tag-'.$i, $userTag);
		}
	}

	public function getOrder()
	{
        return 10;
    }
}