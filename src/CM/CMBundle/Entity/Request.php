<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Request
 *
 * @ORM\Entity(repositoryClass="RequestRepository")
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
     * @var boolean
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = self::STATUS_NEW;

    /**
     * @ORM\Column(name="user_id", type="integer")
     **/
    private $userId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requestsIncoming")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @ORM\Column(name="from_user_id", type="integer")
     **/
    private $fromUserId;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="requestsOutgoing")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $fromUser;

    /**
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     **/
    private $groupId;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="requests")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $group;

    /**
     * @ORM\Column(name="from_page_id", type="integer", nullable=true)
     **/
    private $fromPageId;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="requestsOutgoing")
     * @ORM\JoinColumn(name="from_page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $fromPage;

    /**
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     **/
    private $entityId;

    /**
     * @ORM\ManyToOne(targetEntity="Entity")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $entity;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=50, nullable=true)
     */
    private $object;

    /**
     * @var string
     *
     * @ORM\Column(name="object_id", type="smallint", nullable=true)
     */
    private $objectId;

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

    public function getStatusName()
    {
        switch ($this->status) {
            case self::STATUS_NEW:
                return 'new';
            case self::STATUS_PENDING:
                return 'pending';
            case self::STATUS_ACCEPTED:
                return 'accepted';
            case self::STATUS_REFUSED:
                return 'refused';
        }
    }

    public function isNewOrPending()
    {
        return in_array($this->status, array(self::STATUS_NEW, self::STATUS_PENDING));
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
        if ($user->addRequestIncoming($this)) {
            $this->user = $user;
            $this->userId = $user->getId();
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
        if ($fromUser->addRequestOutgoing($this)) {
            $this->fromUser = $fromUser;
            $this->fromUserId = $fromUser->getId();
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
     * Get fromUser
     *
     * @return User 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     * @return Notification
     */
    public function setGroup(Group $group)
    {
        if ($group->addRequest($this)) {
            $this->group = $group;
            $this->groupId = $group->getId();
        }
    
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getFromPageId()
    {
        return $this->fromPageId;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     * @return Notification
     */
    public function setFromPage(Page $fromPage = null)
    {
        if ($fromPage->addRequest($this)) {
            $this->fromPage = $fromPage;
            $this->fromPageId = $fromPage->getId();
        }
    
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getFromPage()
    {
        return $this->fromPage;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getEntityId()
    {
        return $this->entityId;
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
        $this->object = get_class($entity);
        $this->objectId = $entity->getId();
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
}