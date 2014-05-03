<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Model\ArrayContainer;

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
     * @ORM\OneToMany(targetEntity="EntityUserTag", mappedBy="entityUser", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $entityUserTags;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notification", type="boolean")
     */
    private $notification = true;

    public function __construct()
    {
        $this->entityUserTags = new ArrayCollection;
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
        if (!is_null($entity)) {
            $this->entityId = $entity->getId();
        }
        
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
        if (!is_null($user)) {
            $this->userId = $user->getId();
        }

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
     * @param \CM\CMBundle\Entity\EntityEntityUser $comment
     * @return Entity
     */
    public function addTag(
        Tag $tag,
        $order = null
    )
    {
        if (!$tag->isUser()) return;
        foreach ($this->entityUserTags as $key => $entityUserTag) {
            if ($entityUserTag->getTagId() == $tag->getId()) {
                return;
            }
        }
        $entityUserTag = new EntityUserTag;
        $entityUserTag->setEntityUser($this)
            ->setTag($tag)
            ->setOrder($order);
        $this->entityUserTags[] = $entityUserTag;
    
        return $this;
    }

    public function setEntityUserTags($entityUserTags = array())
    {
        $this->clearTags();
        foreach ($entityUserTags as $order => $entityUserTag) {
            $entityUserTag->setEntityUser($this)
                ->setOrder($order);
            $this->entityUserTags[] = $entityUserTag;
        }

        return $this;
    }

    public function addEntityUserTag($entityUserTag)
    {
        if (!$this->entityUserTags->contains($entityUserTag)) {
            $entityUserTag->setEntityUser($this);
            $this->entityUserTags[] = $entityUserTag;
        }

        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityEntityUser $entityUsers
     */
    public function removeEntityUserTag(EntityUserTag $entityUserTag)
    {
        $this->entityUserTags->removeElement($entityUserTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function clearTags()
    {
        return $this->entityUserTags->clear();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntityUserTags()
    {
        return $this->entityUserTags;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        $tags = array();
        foreach ($this->entityUserTags as $entityUserTag) {
            $tags[] = $entityUserTag->getTag();
        }
        return $tags;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagsIds()
    {
        $tags = array();
        foreach ($this->entityUserTags as $entityUserTag) {
            $tags[] = $entityUserTag->getTagId();
        }
        return $tags;
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