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
 * @ORM\Table(name="user_user_tag")
 */
class UserUserTag
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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="userUserTags")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $user;
    
    /**
     * @ORM\ManyToOne(targetEntity="UserTag", inversedBy="userTagUsers")
     * @ORM\JoinColumn(name="user_tag_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    private $userTag;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="smallint", nullable=true)
     */
    private $order;

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
     * Set entity
     *
     * @param UserTag $entity
     * @return UserUserTag
     */
    public function setUserTag(UserTag $userTag = null)
    {
        $this->userTag = $userTag;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return UserTag
     */
    public function getUserTag()
    {
        return $this->userTag;
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