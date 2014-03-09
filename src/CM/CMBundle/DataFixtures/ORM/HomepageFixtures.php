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
use CM\CMBundle\Entity\HomepageBanner;
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
        array('rubric' => false, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Reviews', 'singular' => 'Review'),
                'it' => array('name' => 'Recensioni', 'singular' => 'Recensione')
            )
        ),
        array('rubric' => false, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Where & when'),
                'it' => array('name' => 'Dove & quando')
            )
        ),
        array('rubric' => false, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'They say about us'),
                'it' => array('name' => 'Dicono di noi')
            )
        ),
        array('rubric' => false, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Actualities', 'singular' => 'Actuality'),
                'it' => array('name' => 'Attualità')
            )
        ),
        array('rubric' => false, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'Interviews', 'singular' => 'Interview'),
                'it' => array('name' => 'Interviste', 'singular' => 'Intervista')
            )
        ),
        array('rubric' => false, 'editor' => 1, 'update' => null,
            'translations' => array(
                'en' => array('name' => 'In-depths', 'singular' => 'In-depth'),
                'it' => array('name' => 'Approfondimenti', 'singular' => 'Approfondimento')
            )
        ),
        array('rubric' => true, 'editor' => 1/*41*/, 'update' => 'Ogni venerdì',
            'translations' => array(
                'en' => array('name' => 'Orchestras', 'singular' => 'Orchestra'),
                'it' => array('name' => 'Orchestre', 'singular' => 'Orchestra')
        )
        )
    );

    private $archives = array(
        array('article' => 1, 'category' => 3),
        // array('article' => 4, 'category' => 0),
        // array('article' => 7, 'category' => 0),
        // array('article' => 8, 'category' => 0),
        // array('article' => 5, 'category' => 0),
        array('article' => 3, 'category' => 1),
        array('article' => 2, 'category' => 3),
    );

    private $boxes = array(
        array('name' => null, 'type' => HomepageBox::TYPE_EVENT, 'category' => null, 'page' => 3, 'logo' => 'la_verdi-title.png', 'colour' => '#c70036'),
        array('name' => null, 'type' => HomepageBox::TYPE_EVENT, 'category' => null, 'page' => 2, 'logo' => 'quartetto_milano-title.png', 'colour' => '#eb6909'),
/*         array('name' => 'Orchestre', 'type' => HomepageBox::TYPE_RUBRIC, 'category' => 0, 'page' => null, 'logo' => null, 'colour' => null), */
/*         array('name' => null, 'type' => HomepageBox::TYPE_ARTICLE, 'category' => null, 'page' => 1, 'logo' => 'accademia_teatro_alla_scala-title.png', 'colour' => '#c70c27'), */
/*         array('name' => null, 'type' => HomepageBox::TYPE_ARTICLE, 'category' => null, 'page' => 2, 'logo' => 'classic_voice-title.png', 'colour' => '#008fd3'), */
        array('name' => null, 'type' => HomepageBox::TYPE_DISC, 'category' => null, 'page' => 1, 'logo' => 'sony_classical.png', 'colour' => '#df2027'),
    );

    private $banners = array(
        array('img' => 'ideamus.jpg', 'alt' => 'ideamus.tv', 'href' => 'http://www.ideamus.tv'),
        array('img' => 'circuitomusica.jpg', 'alt' => 'Circuito Musica', 'href' => 'http://www.circuitomusica.it'),
        array('img' => 'societa_quartetto.jpg', 'alt' => 'Società del Quartetto di Milano', 'href' => 'http://www.quartettomilano.it'),
        array('img' => 'sipario_musicale.jpg', 'alt' => 'il Sipario Musicale', 'href' => 'http://www.ilsipariomusicale.com'),
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
                if (array_key_exists('singular', $trans)) {
                    $homepageCategory->setSingular($trans['singular']);
                }
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

        foreach ($this->banners as $i => $banner) {
            $homepageBanner = new HomepageBanner;
            $homepageBanner->setUser($manager->merge($this->getReference('user-'.rand(1, UserFixtures::count()))))
                ->setImg($banner['img'])
                ->setImgAlt($banner['alt'])
                ->setImgHref($banner['href'])
                ->setPosition($i)
                ->setVisibleFrom(new \DateTime('-1 year'))
                ->setVisibleTo(new \DateTime('+1 year'));

            $manager->persist($homepageBanner);
            $this->addReference('homepage_banner-'.$i, $homepageBanner);
        }

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 110;
    }
}