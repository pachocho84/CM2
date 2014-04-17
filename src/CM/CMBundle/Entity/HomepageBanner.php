<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * HomepageBanner
 *
 * @ORM\Entity(repositoryClass="HomepageBannerRepository")
 * @ORM\Table(name="homepage_banner")
 */
class HomepageBanner
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=50, nullable=false)
     */
    private $img;

    /**
     * @var string
     *
     * @ORM\Column(name="img_alt", type="string", length=250, nullable=false)
     */
    private $imgAlt;

    /**
     * @var string
     *
     * @ORM\Column(name="img_href", type="string", length=250, nullable=false)
     */
    private $imgHref;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="smallint", nullable=false)
     */
    private $position;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visible_from", type="date", nullable=false)
     */
    private $visibleFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="visible_to", type="date", nullable=false)
     */
    private $visibleTo;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $user;

    /**
     * @ORM\Column(name="page_id", type="integer", nullable=true)
     **/
    private $pageId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Page")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $page;

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
     * Set img
     *
     * @param string $img
     * @return HomepageBanner
     */
    public function setImg($img)
    {
        $this->img = $img;
    
        return $this;
    }

    /**
     * Get img
     *
     * @return string 
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set imgAlt
     *
     * @param string $imgAlt
     * @return HomepageBanner
     */
    public function setImgAlt($imgAlt)
    {
        $this->imgAlt = $imgAlt;
    
        return $this;
    }

    /**
     * Get imgAlt
     *
     * @return string 
     */
    public function getImgAlt()
    {
        return $this->imgAlt;
    }

    /**
     * Set imgHref
     *
     * @param string $imgHref
     * @return HomepageBanner
     */
    public function setImgHref($imgHref)
    {
        $this->imgHref = $imgHref;
    
        return $this;
    }

    /**
     * Get imgHref
     *
     * @return string 
     */
    public function getImgHref()
    {
        return $this->imgHref;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return HomepageBanner
     */
    public function setPosition($position)
    {
        $this->position = $position;
    
        return $this;
    }

    /**
     * Get position
     *
     * @return integer 
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set visibleFrom
     *
     * @param \DateTime $visibleFrom
     * @return HomepageBanner
     */
    public function setVisibleFrom($visibleFrom)
    {
        $this->visibleFrom = $visibleFrom;
    
        return $this;
    }

    /**
     * Get visibleFrom
     *
     * @return \DateTime 
     */
    public function getVisibleFrom()
    {
        return $this->visibleFrom;
    }

    /**
     * Set visibleTo
     *
     * @param \DateTime $visibleTo
     * @return HomepageBanner
     */
    public function setVisibleTo($visibleTo)
    {
        $this->visibleTo = $visibleTo;
    
        return $this;
    }

    /**
     * Get visibleTo
     *
     * @return \DateTime 
     */
    public function getVisibleTo()
    {
        return $this->visibleTo;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        if (!is_null($user)) {
            $this->userId = $user->getId();
        }
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
        if (!is_null($page)) {
            $this->pageId = $page->getId();
        }
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getPage()
    {
        return $this->page;
    }
}
