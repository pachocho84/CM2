<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\EntityCategoryEnum;

class EventCategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
	public function load(ObjectManager $manager)
    {
    	$category1 = new EntityCategory;
    	$category1
    		->setEntityType(EntityCategory::EVENT)
    		->setName('concert')
    		->setPlural('concerts');
    	$category1->translate('fr')
    		->setName('concert')
    		->setPlural('concerts');
    	$category1->translate('it')
    		->setName('concerto')
    		->setPlural('concerti');
    		
    	$manager->persist($category1);
    	$category1->mergeNewTranslations();
    	
    	$category2 = new EntityCategory;
    	$category2
    		->setEntityType(EntityCategory::EVENT)
    		->setName('opera')
    		->setPlural('operas');
    	$category2->translate('fr')
    		->setName('opéra')
    		->setPlural('opéras');
    	$category2->translate('it')
    		->setName('opera')
    		->setPlural('opere');
    		
    	$manager->persist($category2);
    	$category2->mergeNewTranslations();
    	
    	$category3 = new EntityCategory;
    	$category3
    		->setEntityType(EntityCategory::EVENT)
    		->setName('masterclass')
    		->setPlural('masterclasses');
    	$category3->translate('fr')
    		->setName('cours de maître')
    		->setPlural('cours de maître');
    	$category3->translate('it')
    		->setName('masterclass')
    		->setPlural('masterclass');
    		
    	$manager->persist($category3);
    	$category3->mergeNewTranslations();

        $manager->flush();
        
        $this->addReference('entity_category-1', $category1);
        $this->addReference('entity_category-2', $category2);
        $this->addReference('entity_category-3', $category3);
    }

    public function getOrder()
    {
        return 1;
    }
}