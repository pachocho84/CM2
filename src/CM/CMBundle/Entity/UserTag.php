<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

// TODO: reinsert unique constraint:
/*
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "user_id", "user_tag_id"
 *     })}
 */
 
/**
 * UserUserTag
 *
 * @ORM\Entity
 * @ORM\Table(name="user_tag")
 */
class UserTag
{     
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="CM\CMBundle\Entity\User", inversedBy="userTags")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(name="tag_id", type="integer")
     */
    private $tagId;
    
    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="tagUsers")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="smallint", nullable=true)
     */
    private $order;

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
     * Set entity
     *
     * @param UserTag $entity
     * @return UserUserTag
     */
    public function setTag(Tag $tag = null)
    {
        if (!$tag->isUser()) return;
        $this->tag = $tag;
        if (!is_null($tag)) {
            $this->tagId = $tag->getId();
        }
    
        return $this;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getTagId()
    {
        return $this->tagId;
    }

    /**
     * Get entity
     *
     * @return UserTag
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set order
     *
     * @param integer $order
     * @return UserUserTag
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get order
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }
}