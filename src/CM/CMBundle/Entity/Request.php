<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Request
 *
 * @ORM\Entity
 * @ORM\Table(name="request")
 */
class Request
{
    use ORMBehaviors\Timestampable\Timestampable;
    
    const STATUS_NEW = 0;
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REFUSED = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requestsIncoming")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requestsOutgoing")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $fromUser;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="requestsOutgoing")
     * @ORM\JoinColumn(name="from_group_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $fromGroup;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="requestsOutgoing")
     * @ORM\JoinColumn(name="from_page_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $fromPage;

    /**
     * @ORM\ManyToOne(targetEntity="Entity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=50)
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="object_id", type="smallint")
     */
    private $objectId;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = self::STATUS_NEW;

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
     * Set object
     *
     * @param string $object
     * @return Request
     */
    public function setObject($object)
    {
        $this->object = $object;
    
        return $this;
    }

    /**
     * Get object
     *
     * @return string 
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     * Set objectId
     *
     * @param integer $objectId
     * @return Request
     */
    public function setObjectId($objectId)
    {
        $this->objectId = $objectId;
    
        return $this;
    }

    /**
     * Get objectId
     *
     * @return integer 
     */
    public function getObjectId()
    {
        return $this->objectId;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return Request
     */
    public function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_NEW, self::STATUS_PENDING, 
                self::STATUS_ACCEPTED, self::STATUS_REFUSED))) {
            throw new \InvalidArgumentException("Invalid status");
        }

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
     * Set user
     *
     * @param User $user
     * @return Request
     */
    public function setUser(User $user)
    {
        if ($user->addRequestIncoming($this)) {
            $this->user = $user;
        }
    
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
     * Set fromUser
     *
     * @param User $fromUser
     * @return Request
     */
    public function setFromUser(User $fromUser)
    {
        if ($user->addRequestOutgoing($this)) {
            $this->fromUser = $fromUser;
        }
    
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

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Request
     */
    public function setEntity(Entity $entity)
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
}