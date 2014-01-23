<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\HomepageCategory;
use CM\CMBundle\Entity\HomepageArchive;
use CM\CMBundle\Entity\HomepageBox;
use CM\CMBundle\Entity\HomepageRow;
use CM\CMBundle\Entity\HomepageColumn;

class HomepageFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private $categories = array(
        array('rubric' => 0, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Recensioni'),
                'it' => array('name' => 'Recensioni')
            )
        ),
        array('rubric' => 0, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Dove & Quando'),
                'it' => array('name' => 'Dove & Quando')
            )
        ),
        array('rubric' => 0, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Dicono di Noi'),
                'it' => array('name' => 'Dicono di Noi')
            )
        ),
        array('rubric' => 0, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Attualità'),
                'it' => array('name' => 'Attualità')
            )
        ),
        array('rubric' => 0, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Interviste'),
                'it' => array('name' => 'Interviste')
            )
        ),
        array('rubric' => 0, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Approfondimenti'),
                'it' => array('name' => 'Approfondimenti')
            )
        ),
        array('rubric' => 1, 'editor' => 1/*41*/, 'update' => 'Ogni venerdì',
            'translations' => array(
                'en' => array('name' => 'Orchestre'),
                'it' => array('name' => 'Orchestre')
        )
        )
    );

    private $archives = array(
        array('user' => 1, 'article' => 1, 'category' => 3),
        array('user' => 1, 'article' => 4, 'category' => 0),
        array('user' => 1, 'article' => 2, 'category' => 0),
        array('user' => 1, 'article' => 8, 'category' => 0),
        array('user' => 1, 'article' => 5, 'category' => 0),
        array('user' => 1/*115*/, 'article' => 3, 'category' => 1),
        array('user' => 1/*115*/, 'article' => 7, 'category' => 3),
    );

    private $boxes = array(
        array('name' => 'laVerdi', 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 3, 'width' => HomepageBox::WIDTH_HALF, 'leftSide' => HomepageBox::SIDE_EVENTS, 'rightSide' => null),
        array('name' => 'Orchestre', 'type' => HomepageBox::TYPE_RUBRIC, 'category' => 0, 'page' => null, 'width' => HomepageBox::WIDTH_HALF, 'leftSide' => HomepageBox::SIDE_ARTICLES, 'rightSide' => null),
        array('name' => 'Accademia Teatro alla Scala', 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 1/*7*/, 'width' => HomepageBox::WIDTH_HALF, 'leftSide' => HomepageBox::SIDE_NEWS, 'rightSide' => null),
        array('name' => 'Classic Voice', 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 2/*6*/, 'width' => HomepageBox::WIDTH_HALF, 'leftSide' => HomepageBox::SIDE_ARTICLES, 'rightSide' => null),
    );

    private $rows = array(
        array('type' => HomepageRow::TYPE_ROW_1, 'order' => 1, 'visible' => true,
            'columns' => array(
                array('row' => 0, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 3, 'box' => null, 'user' => 1, 'order' => 1),
            )
        ),
        array('type' => HomepageRow::TYPE_ROW_2, 'order' => 2, 'visible' => true,
            'columns' => array(
                array('row' => 1, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 1, 'box' => null, 'user' => 1, 'order' => 2),
                array('row' => 1, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 0, 'box' => null, 'user' => 1, 'order' => 1),
            )
        ),
        array('type' => HomepageRow::TYPE_PHOTO_GALLERY, 'order' => 6, 'visible' => true, 'columns' => array()),
        array('type' => HomepageRow::TYPE_ROW_3, 'order' => 8, 'visible' => true,
            'columns' => array(
                array('row' => 3, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 5, 'box' => null, 'user' => 1, 'order' => 1),
                array('row' => 3, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 4, 'box' => null, 'user' => 1, 'order' => 2),
                array('row' => 3, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 0, 'box' => null, 'user' => 1, 'order' => 3),
            )
        ),
        array('type' => HomepageRow::TYPE_VIDEO_GALLERY, 'order' => 10, 'visible' => true, 'columns' => array()),
        array('type' => HomepageRow::TYPE_ROW_4, 'order' => 11, 'visible' => true,
            'columns' => array(
                array('row' => 5, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 0, 'box' => null, 'user' => 1, 'order' => 1),
                array('row' => 5, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 2, 'box' => null, 'user' => 1, 'order' => 3),
                array('row' => 5, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 3, 'box' => null, 'user' => 1, 'order' => 4),
                array('row' => 5, 'type' => HomepageColumn::TYPE_ARTICLE, 'archive' => 4, 'box' => null, 'user' => 1, 'order' => 2),
            )
        ),
        array('type' => HomepageRow::TYPE_PARTNER_ACCADEMIA_TEATRO_ALLA_SCALA, 'order' => 9, 'visible' => true, 'columns' => array()),
        array('type' => HomepageRow::TYPE_PARTNER_RADIO_CLASSICA, 'order' => 7, 'visible' => true, 'columns' => array()),
        array('type' => HomepageRow::TYPE_REVIEWS, 'order' => 5, 'visible' => true, 'columns' => array()),
        array('type' => HomepageRow::TYPE_BANNER_SUBSCRIBE, 'order' => 8, 'visible' => true, 'columns' => array()),
        array('type' => HomepageRow::TYPE_ROW_2, 'order' => 4, 'visible' => true,
            'columns' => array(
                array('row' => 10, 'type' => HomepageColumn::TYPE_BOX, 'archive' => null, 'box' => 1, 'user' => 1, 'order' => 2),
                array('row' => 10, 'type' => HomepageColumn::TYPE_BOX, 'archive' => null, 'box' => 0, 'user' => 1, 'order' => 1),
            )
        ),
        array('type' => HomepageRow::TYPE_ROW_2, 'order' => 3, 'visible' => true,
            'columns' => array(
                array('row' => 11, 'type' => HomepageColumn::TYPE_BOX, 'archive' => null, 'box' => 2, 'user' => 1, 'order' => 1),
                array('row' => 11, 'type' => HomepageColumn::TYPE_BOX, 'archive' => null, 'box' => 3, 'user' => 1, 'order' => 1),
            )
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
        foreach ($this->categories as $i => $category) {
            $homepageCategory = new HomepageCategory;
            $homepageCategory->setRubric($category['rubric']);
            $homepageCategory->setEditor($manager->merge($this->getReference('user-'.$category['editor'])));
            $homepageCategory->setUpdate($category['update']);
            foreach ($category['translations'] as $lang => $trans) {
                $homepageCategory->translate($lang)
                    ->setName($trans['name']);
            }
            $homepageCategory->mergeNewTranslations();

            $manager->persist($homepageCategory);
            $this->addReference('homepage_category-'.$i, $homepageCategory);
        }

        $manager->flush();

        foreach ($this->archives as $i => $archive) {
            $homepageArchive = new HomepageArchive;
            $homepageArchive->setUser($manager->merge($this->getReference('user-'.$archive['user'])))
                ->setArticle($manager->merge($this->getReference('article-'.$archive['article'])))
                ->setCategory($manager->merge($this->getReference('homepage_category-'.$archive['category'])));

            $manager->persist($homepageArchive);
            $this->addReference('homepage_archive-'.$i, $homepageArchive);
        }

        $manager->flush();

        foreach ($this->boxes as $i => $box) {
            $homepageBox = new HomepageBox;
            $homepageBox->setName($box['name'])
                ->setType($box['type'])
                ->setWidth($box['width'])
                ->setLeftSide($box['leftSide'])
                ->setRightSide($box['rightSide']);
            if (!is_null($box['category'])) {
                $homepageBox->setCategory($manager->merge($this->getReference('homepage_category-'.$box['category'])));
            }
            if (!is_null($box['page'])) {
                $homepageBox->setPage($manager->merge($this->getReference('page-'.$box['page'])));
            }

            $manager->persist($homepageBox);
            $this->addReference('homepage_box-'.$i, $homepageBox);
        } 

        foreach ($this->rows as $i => $row) {
            $homepageRow = new HomepageRow;
            $homepageRow->setType($row['type'])
                ->setOrder($row['order'])
                ->setVisible($row['visible']);

            foreach ($row['columns'] as $j => $column) {
                $homepageColumn = new HomepageColumn;
                $homepageColumn->setType($column['type'])
                    ->setUser($manager->merge($this->getReference('user-'.$column['user'])))
                    ->setOrder($column['order']);
                if (!is_null($column['archive'])) {
                    $homepageColumn->setArchive($manager->merge($this->getReference('homepage_archive-'.$column['archive'])));
                }
                if (!is_null($column['box'])) {
                    $homepageColumn->setBox($manager->merge($this->getReference('homepage_box-'.$column['box'])));
                }

                $homepageRow->addColumn($homepageColumn);
                // $this->addReference('homepage_column-'.$j, $homepageColumn);
            } 

            $manager->persist($homepageRow);
            $this->addReference('homepage_row-'.$i, $homepageRow);
        }

        $manager->flush();


        $manager->flush();
    }
    
    public function getOrder()
    {
        return 110;
    }
}