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
    use ORMBehaviors\Timestampable\Timestampable;

    const TYPE_PARTNER = 0;
    const TYPE_RUBRIC = 1;

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
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=50, nullable=true)
     */
    private $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="colour", type="string", length=50, nullable=true)
     */
    private $colour;

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
     * Set logo
     *
     * @param string $logo
     * @return HomepageBox
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    
        return $this;
    }

    /**
     * Get logo
     *
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set colour
     *
     * @param string $colour
     * @return HomepageBox
     */
    public function setColour($colour)
    {
        $this->colour = $colour;
    
        return $this;
    }

    /**
     * Get colour
     *
     * @return string 
     */
    public function getColour()
    {
        return $this->colour;
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
}
