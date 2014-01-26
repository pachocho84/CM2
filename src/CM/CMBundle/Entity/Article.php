<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Entity(repositoryClass="ArticleRepository")
 * @ORM\Table(name="article")
 */
class Article extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="HomepageArchive", mappedBy="article", cascade={"persist", "remove"})
     **/
    private $homepageArchive = null;

    public function __construct()
    {
        parent::__construct();
        
        $this->translate('en');
        $this->mergeNewTranslations();
    }

    public function __toString()
    {
        return is_null($this->getText()) ? '' : $this->getText();
    }

    public static function className()
    {
        return get_class();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set homepageArchive
     *
     * @param string $homepageArchive
     * @return Article
     */
    public function setHomepageArchive($homepageArchive)
    {
        $this->homepageArchive = $homepageArchive;
        if (!is_null($homepageArchive)) {
            $homepageArchive->setArticle($this);
        }
    
        return $this;
    }

    /**
     * Get homepageArchive
     *
     * @return string 
     */
    public function getHomepageArchive()
    {
        return $this->homepageArchive;
    }
}
