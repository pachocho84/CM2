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

        $category6 = new EntityCategory;
        $category6->setEntityType(EntityCategory::ARTICLE)
            ->setName('news')
            ->setPlural('news');
        $category6->translate('fr')
            ->setName('actualité')
            ->setPlural('actualités');
        $category6->translate('it')
            ->setName('news')
            ->setPlural('news');
        $manager->persist($category6);
        $category6->mergeNewTranslations();
        $this->addReference('article_category-1', $category6);

        $category7 = new EntityCategory;
        $category7->setEntityType(EntityCategory::ARTICLE)
            ->setName('article')
            ->setPlural('articles');
        $category7->translate('fr')
            ->setName('article')
            ->setPlural('articles');
        $category7->translate('it')
            ->setName('articolo')
            ->setPlural('articoli');
        $manager->persist($category7);
        $category7->mergeNewTranslations();
        $this->addReference('article_category-2', $category7);

        $category8 = new EntityCategory;
        $category8->setEntityType(EntityCategory::ARTICLE)
            ->setName('review')
            ->setPlural('reviews');
        $category8->translate('fr')
            ->setName('critique')
            ->setPlural('critiques');
        $category8->translate('it')
            ->setName('recensione')
            ->setPlural('recensioni');
        $manager->persist($category8);
        $category8->mergeNewTranslations();
        $this->addReference('article_category-3', $category8);

        $category9 = new EntityCategory;
        $category9->setEntityType(EntityCategory::ARTICLE)
            ->setName('book')
            ->setPlural('books');
        $category9->translate('fr')
            ->setName('livre')
            ->setPlural('livres');
        $category9->translate('it')
            ->setName('libro')
            ->setPlural('libri');
        $manager->persist($category9);
        $category9->mergeNewTranslations();
        $this->addReference('article_category-4', $category9);

        $category10 = new EntityCategory;
        $category10->setEntityType(EntityCategory::ARTICLE)
            ->setName('sheet music')
            ->setPlural('sheet musics');
        $category10->translate('fr')
            ->setName('partition')
            ->setPlural('partitions');
        $category10->translate('it')
            ->setName('spartito')
            ->setPlural('spartiti');
        $manager->persist($category10);
        $category10->mergeNewTranslations();
        $this->addReference('article_category-5', $category10);

        $category11 = new EntityCategory;
        $category11->setEntityType(EntityCategory::ARTICLE)
            ->setName('press release')
            ->setPlural('press releases');
        $category11->translate('fr')
            ->setName('presse écrite')
            ->setPlural('presse écrites');
        $category11->translate('it')
            ->setName('comunicato stampa')
            ->setPlural('comunicati stampa');
        $manager->persist($category11);
        $category11->mergeNewTranslations();
        $this->addReference('article_category-6', $category11);

        $category12 = new EntityCategory;
        $category12->setEntityType(EntityCategory::ARTICLE)
            ->setName('interview')
            ->setPlural('interviews');
        $category12->translate('fr')
            ->setName('interview')
            ->setPlural('interview');
        $category12->translate('it')
            ->setName('intervista')
            ->setPlural('interviste');
        $manager->persist($category12);
        $category12->mergeNewTranslations();
        $this->addReference('article_category-7', $category12);

        $manager->flush();
        
    }

    public function getOrder()
    {
        return 50;
    }
}