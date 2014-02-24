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

    const ACCEPTED_NO = 0;
    const ACCEPTED_UNI = 1;
    const ACCEPTED_BOTH = 2;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="relation_type", type="integer")
     **/
    private $relationTypeId;

    /**
     * @ORM\ManyToOne(targetEntity="RelationType", inversedBy="relations")
     * @ORM\JoinColumn(name="relation_type", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $relationType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accepted", type="smallint", nullable=false)
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

    // public function __toString()
    // {
    //     return $this->relationType();
    // }

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
     * Get type
     *
     * @return integer 
     */
    public function getRelationTypeId()
    {
        return $this->relationTypeId;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return Relation
     */
    public function setRelationType($relationType)
    {
        $this->relationType = $relationType;
        if (!is_null($relationType)) {
            $this->relationTypeId = $relationType->getId();
        }
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getRelationType()
    {
        return $this->relationType;
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
