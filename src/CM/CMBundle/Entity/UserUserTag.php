<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserUserTag
 *
 * @ORM\Entity
 * @ORM\Table(name="user_user_tag")
 */
class UserUserTag
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="User", inversedBy="usersUserTags")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;
    
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="UserTag", inversedBy="usersUserTags")
     * @ORM\JoinColumn(name="user_tag_id", referencedColumnName="id", nullable=false)
     */
    private $userTag;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="smallint", nullable=true)
     */
    private $order;

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