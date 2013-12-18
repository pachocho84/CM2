<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CM\CMBundle\Entity\EntityCategory;

class EntityCategoryFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $category1 = new EntityCategory;
        $category1->setEntityType(EntityCategory::EVENT)
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
        $this->addReference('event_category-1', $category1);
        
        $category2 = new EntityCategory;
        $category2->setEntityType(EntityCategory::EVENT)
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
        $this->addReference('event_category-2', $category2);
        
        $category3 = new EntityCategory;
        $category3->setEntityType(EntityCategory::EVENT)
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
        $this->addReference('event_category-3', $category3);

        $category4 = new EntityCategory;
        $category4->setEntityType(EntityCategory::DISC)
            ->setName('CD')
            ->setPlural('CDs');
        $category4->translate('fr')
            ->setName('CD')
            ->setPlural('CD');
        $category4->translate('it')
            ->setName('CD')
            ->setPlural('CD');
        $manager->persist($category4);
        $category4->mergeNewTranslations();
        $this->addReference('disc_category-1', $category4);
        
        $category5 = new EntityCategory;
        $category5->setEntityType(EntityCategory::DISC)
            ->setName('DVD')
            ->setPlural('DVDs');
        $category5->translate('fr')
            ->setName('DVD')
            ->setPlural('DVD');
        $category5->translate('it')
            ->setName('DVD')
            ->setPlural('DVD');
        $manager->persist($category5);
        $category5->mergeNewTranslations();
        $this->addReference('disc_category-2', $category5);

        $manager->flush();
        
    }

    public function getOrder()
    {
        return 50;
    }
}