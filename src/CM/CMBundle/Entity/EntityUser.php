<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntityUser
 *
 * @ORM\Entity
 * @ORM\Table(name="entity_user")
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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="entitiesUsers")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", nullable=false)
     */
    private $entity;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="entitiesUsers")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
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

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return EntityUser
     */
    public function setEntity(Entity $entity = null)
    {
        $this->entity = $entity;
    
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
     * Set entity
     *
     * @param User $entity
     * @return EntityUser
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
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
     * Add userTag
     *
     * @param UserTag $userTag
     * @return EntityUser
     */
    public function addUserTag($userTag)
    {
        if (!in_array($userTag, $this->getUserTags())) {
            $this->userTags[] = $userTag;
        }
        
        return $this;
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
}
