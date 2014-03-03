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
        array('article' => 1, 'category' => 3),
        array('article' => 4, 'category' => 0),
        array('article' => 2, 'category' => 0),
        array('article' => 8, 'category' => 0),
        array('article' => 5, 'category' => 0),
        array('article' => 3, 'category' => 1),
        array('article' => 7, 'category' => 3),
    );

    private $boxes = array(
        array('name' => null, 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 3, 'logo' => 'la_verdi-title.png', 'colour' => '#c70036'),
        array('name' => null, 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 2, 'logo' => 'quartetto_milano-title.png', 'colour' => '#eb6909'),
        array('name' => 'Orchestre', 'type' => HomepageBox::TYPE_RUBRIC, 'category' => 0, 'page' => null, 'logo' => null, 'colour' => null),
        array('name' => null, 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 1/*7*/, 'logo' => 'accademia_teatro_alla_scala-title.png', 'colour' => '#c70c27'),
        array('name' => null, 'type' => HomepageBox::TYPE_PARTNER, 'category' => null, 'page' => 2/*6*/, 'logo' => 'classic_voice-title.png', 'colour' => '#008fd3'),
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
            $homepageArchive->setArticle($manager->merge($this->getReference('article-'.$archive['article'])))
                ->setCategory($manager->merge($this->getReference('homepage_category-'.$archive['category'])));

            $manager->persist($homepageArchive);
            $this->addReference('homepage_archive-'.$i, $homepageArchive);
        }

        $manager->flush();

        foreach ($this->boxes as $i => $box) {
            $homepageBox = new HomepageBox;
            $homepageBox->setName($box['name'])
                ->setType($box['type'])
                ->setLogo($box['logo'])
                ->setColour($box['colour'])
                ->setPosition($i)
                ->setVisibleFrom(new \DateTime('-1 year'))
                ->setVisibleTo(new \DateTime('+1 year'));
            if (!is_null($box['category'])) {
                $homepageBox->setCategory($manager->merge($this->getReference('homepage_category-'.$box['category'])));
            }
            if (!is_null($box['page'])) {
                $homepageBox->setPage($manager->merge($this->getReference('page-'.$box['page'])));
            }

            $manager->persist($homepageBox);
            $this->addReference('homepage_box-'.$i, $homepageBox);
        }

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 110;
    }
}