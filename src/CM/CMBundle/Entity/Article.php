<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Article
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\ArticleRepository")
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
     * @ORM\Column(name="homepage", type="boolean")
     */
    private $homepage = false;

    /**
     * @ORM\OneToOne(targetEntity="HomepageArchive", mappedBy="article", cascade={"persist", "remove"})
     **/
    private $homepageArchive = null;

    /**
     * @var string
     *
     * @ORM\Column(name="source", type="string", length=100)
     */
    private $source;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

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
     * Set homepage
     *
     * @param string $homepage
     * @return Article
     */
    public function setHomepage($homepage)
    {
        $this->homepage = $homepage;
    
        return $this;
    }

    /**
     * Get homepage
     *
     * @return string 
     */
    public function getHomepage()
    {
        return $this->homepage;
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

    /**
     * Set source
     *
     * @param string $source
     * @return Article
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Article
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
}
