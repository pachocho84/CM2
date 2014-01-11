<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * HomepageBox
 *
 * @ORM\Entity(repositoryClass="HomepageBoxRepository")
 * @ORM\Table(name="homepage_box")
 */
class HomepageBox
{
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\Timestampable\Timestampable;

    const TYPE_PARTNER = 0;
    const TYPE_RUBRIC = 1;

    const WIDTH_FULL = 0;
    const WIDTH_HALF = 1;

    const SIDE_EVENTS = 0;
    const SIDE_ARTICLES = 1;
    const SIDE_NEWS = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="width", type="smallint", nullable=false)
     */
    private $width;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="category_id", type="integer", nullable=true)
     */
    private $categoryId;

    /**
     * @ORM\ManyToOne(targetEntity="HomepageCategory")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $category;

    /**
     * @ORM\Column(name="page_id", type="integer", nullable=true)
     */
    private $pageId;

    /**
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $page;

    /**
     * @var integer
     *
     * @ORM\Column(name="leftSide", type="smallint", nullable=false)
     */
    private $leftSide;

    /**
     * @var integer
     *
     * @ORM\Column(name="rightSide", type="smallint", nullable=true)
     */
    private $rightSide;

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
     * Set width
     *
     * @param integer $width
     * @return HomepageBox
     */
    public function setWidth($width)
    {
        $this->width = $width;
    
        return $this;
    }

    /**
     * Get width
     *
     * @return integer 
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return HomepageBox
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return HomepageBox
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setCategory(HomepageCategory $category)
    {
        $this->category = $category;
        if (!is_null($category)) {
            $this->categoryId = $category->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setPage(Page $page)
    {
        $this->page = $page;
        if (!is_null($page)) {
            $this->pageId = $page->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Set leftSide
     *
     * @param integer $leftSide
     * @return HomepageBox
     */
    public function setLeftSide($leftSide)
    {
        $this->leftSide = $leftSide;
    
        return $this;
    }

    /**
     * Get leftSide
     *
     * @return integer 
     */
    public function getLeftSide()
    {
        return $this->leftSide;
    }

    /**
     * Set rightSide
     *
     * @param integer $rightSide
     * @return HomepageBox
     */
    public function setRightSide($rightSide)
    {
        $this->rightSide = $rightSide;
    
        return $this;
    }

    /**
     * Get rightSide
     *
     * @return integer 
     */
    public function getRightSide()
    {
        return $this->rightSide;
    }

    /**
     * Get sluggable fields
     * 
     * @access public
     * @return void
     */
    public function getSluggableFields()
    {
        return ['name'];
    }
}
