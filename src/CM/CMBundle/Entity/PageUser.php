<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

// TODO: reinsert unique constraint:
/*
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "page_id", "user_id"
 *     })}
 */

/**
 * EntityUser
 *
 * @ORM\Entity(repositoryClass="PageUserRepository")
 * @ORM\Table(name="page_user")
 */
class PageUser
{
    use ORMBehaviors\Timestampable\Timestampable;

    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REQUESTED = 2;
    const STATUS_REFUSED_ADMIN = 3;
    const STATUS_REFUSED_PAGE_USER = 4;
    
    const JOIN_NO = 0;
    const JOIN_YES = 1;
    const JOIN_REQUEST = 2;
            
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="page_id", type="integer")
     */
    private $pageId;
                
    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="pageUsers")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $page;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userPages")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @var boolean
     *
     * @ORM\Column(name="admin", type="boolean")
     */
    private $admin = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var integer
     *
     * @ORM\Column(name="join_event", type="smallint", nullable=false)
     */
    private $joinEvent = self::JOIN_REQUEST;

    /**
     * @var integer
     *
     * @ORM\Column(name="join_disc", type="smallint", nullable=false)
     */
    private $joinDisc = self::JOIN_REQUEST;

    /**
     * @var integer
     *
     * @ORM\Column(name="join_article", type="smallint", nullable=false)
     */
    private $joinArticle = self::JOIN_REQUEST;
    
    /**
     * @var array
     *
     * @ORM\Column(name="user_tags", type="simple_array", nullable=true)
     */
    private $userTags = array();

    /**
     * @var boolean
     *
     * @ORM\Column(name="notification", type="boolean")
     */
    private $notification = true;

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
     * Get entity
     *
     * @return User 
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return EntityUser
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
        $this->pageId = $page->getId();
    
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

    /**
     * Get entity
     *
     * @return User 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set entity
     *
     * @param User $entity
     * @return EntityUser
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        $this->userId = $user->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set admin
     *
     * @param boolean $admin
     * @return EntityUser
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    
        return $this;
    }

    /**
     * Get admin
     *
     * @return boolean 
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return EntityUser
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     *
     * @param integer $joinEvent
     * @return EntityUser
     */
    public function setJoinEvent($status)
    {
        $this->joinEvent = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getJoinEvent()
    {
        return $this->joinEvent;
    }

    /**
     * Set status
     *
     * @param integer $joinEvent
     * @return EntityUser
     */
    public function setJoinDisc($status)
    {
        $this->joinDisc = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getJoinDisc()
    {
        return $this->joinDisc;
    }

    /**
     * Set status
     *
     * @param integer $joinEvent
     * @return EntityUser
     */
    public function setJoinArticle($status)
    {
        $this->joinArticle = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getJoinArticle()
    {
        return $this->joinArticle;
    }
    
    /**
     * Add userTag
     *
     * @param UserTag $userTag
     * @return EntityUser
     */
    public function addUserTags(array $userTags)
    {
        foreach ($userTags as $userTag) {
            $this->addUserTag($userTag);
        }
        
        return $this;
    }
    
    /**
     * Add userTag
     *
     * @param UserTag $userTag
     * @return EntityUser
     */
    public function addUserTag($userTag)
    {
        if (!in_array($userTag, $this->userTags)) {
            $this->userTags[] = $userTag;
        }
        
        return $this;
    }
    
    /**
     * Remove userTag
     *
     * @param UserTag $userTag
     * return EntityUser
     */
    public function removeUserTag($userTag)
    {
        if(($key = array_search($userTag, $this->getUserTags())) !== false) {
            unset($this->userTags[$key]);
        }
    }
    
    /**
     * Get userTags
     *
     * @return array
     */
    public function getUserTags()
    {
        return $this->userTags;
    }
    /**
     * Add userTag
     *
     * @param UserTag $userTag
     * @return EntityUser
     */
    public function hasUserTag($userTag)
    {
        return in_array($userTag, $this->userTags);
    }

    /**
     * Set notification
     *
     * @param boolean $nototification
     * @return EntityUser
     */
    public function setNotification($notification)
    {
        $this->notification = $notification;
    
        return $this;
    }

    /**
     * Get notification
     *
     * @return boolean 
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
