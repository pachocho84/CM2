<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;

//UNIQ_C55F6F6281257D5DA76ED395

// TODO: reinsert unique constraint:
/*
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "entity_id", "user_id"
 *     })}
 */

/**
 * EntityUser
 *
 * @ORM\Entity(repositoryClass="EntityUserRepository")
 * @ORM\Table(name="entity_user")
 * @ORM\HasLifecycleCallbacks
 */
class EntityUser
{
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_REQUESTED = 2;
    const STATUS_REFUSED_ADMIN = 3;
    const STATUS_REFUSED_ENTITY_USER = 4;
    const STATUS_FOLLOWING = 5;
            
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="entity_id", type="integer")
     */
    private $entityId;
        
    /**
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="entityUsers")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $entity;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userEntities")
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
    
    private $isNew = false;
    private $isUpdated = false;

    public function __construct()
    {
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
     * Get entityId
     *
     * @return Entity 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return EntityUser
     */
    public function setEntity(Entity $entity = null)
    {
        $this->entity = $entity;
        $this->entityId = $entity->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getEntity()
    {
        return $this->entity;
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
    public function isAdmin()
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

    public function isNew()
    {
        return $this->isNew;
    }

    public function isUpdated()
    {
        return $this->isUpdated;
    }
    
    /**
     * @ORM\PrePersist
     */
    protected function checkINew()
    {
        $this->isNew = true;
    }
    
    /**
     * @ORM\PreUpdate
     */
    protected function checkIsUpdated()
    {
        $this->isUpdated = true;
    }
}