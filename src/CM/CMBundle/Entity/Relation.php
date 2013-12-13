<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Relation
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\RelationRepository")
 * @ORM\Table(name="relation")
 */
class Relation
{
    use ORMBehaviors\Timestampable\Timestampable;
    
    const TYPE_TEACHER = 1;
    const TYPE_STUDENT = -1;
    const TYPE_AGENT = 2;
    const TYPE_CLIENT = -2;

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
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accepted", type="boolean")
     */
    private $accepted;

    /**
     * @ORM\Column(name="user_id", type="integer")
     **/
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="relationsIncoming")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @ORM\Column(name="from_user_id", type="integer")
     **/
    private $fromUserId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="relationsOutgoing")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $fromUser;

    public function __toString()
    {
        switch ($this->type) {
            case self::TYPE_TEACHER:
                return 'teacher';
            case self::TYPE_STUDENT:
                return 'student';
            case self::TYPE_AGENT:
                return 'agent';
            case self::TYPE_CLIENT:
                return 'client';
            default:
                return '';
        }
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

    public static function inverseType($type)
    {
        return -$type;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Relation
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
     * Get type
     *
     * @return integer 
     */
    public function getInverseType()
    {
        return $this->inverseType($this->type);
    }

    /**
     * Set accepted
     *
     * @param boolean $accepted
     * @return Relation
     */
    public function setAccepted($accepted)
    {
        $this->accepted = $accepted;
    
        return $this;
    }

    /**
     * Get accepted
     *
     * @return boolean 
     */
    public function getAccepted()
    {
        return $this->accepted;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Request
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->userId = $user->getId();
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getFromUserId()
    {
        return $this->fromUserId;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     * @return Request
     */
    public function setFromUser(User $fromUser)
    {
        $this->fromUser = $fromUser;
        $this->fromUserId = $fromUser->getId();
    
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return \CM\UserBundle\Entity\User 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }
}
