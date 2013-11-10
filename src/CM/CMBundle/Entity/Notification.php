<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Notification
 *
 * @ORM\Entity(repositoryClass="NotificationRepository")
 * @ORM\Table(name="notification")
 */
class Notification
{
    use ORMBehaviors\Timestampable\Timestampable;
    
    const TYPE_LIKE = 0;
    const TYPE_COMMENT = 1;
    const TYPE_FAN = 2;
    const TYPE_REQUEST_ACCEPTED = 3;

    const STATUS_NEW = 0;
    const STATUS_NOTIFIED = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="smallint", nullable=false)
     */
    private $status = self::STATUS_NEW;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notificationsIncoming")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="notificationsOutgoing")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $fromUser;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="notificationsOutgoing")
     * @ORM\JoinColumn(name="from_group_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $fromGroup;

    /**
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="notificationsOutgoing")
     * @ORM\JoinColumn(name="from_page_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $fromPage;

    /**
     * @ORM\ManyToOne(targetEntity="Post")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $post;

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

    private $relatedObject;

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
     * Set type
     *
     * @param string $type
     * @return Post
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set status
     *
     * @param boolean $status
     * @return Notification
     */
    public function setStatus($status)
    {
        if (!in_array($status, array(self::STATUS_NEW, self::STATUS_NOTIFIED))) {
            throw new \InvalidArgumentException("Invalid status");
        }

        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return boolean 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Notification
     */
    public function setUser(User $user = null)
    {
        if ($user->addNotificationIncoming($this)) {
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
     * @return Notification
     */
    public function setFromUser(User $fromUser = null)
    {
        if ($fromUser->addNotificationOutgoing($this)) {
            $this->fromUser = $fromUser;
        }
    
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     * @return Notification
     */
    public function setFromGroup(User $fromGroup = null)
    {
        if ($fromGroup->addNotificationOutgoing($this)) {
            $this->fromGroup = $fromGroup;
        }
    
        return $this;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getFromGroup()
    {
        return $this->fromGroup;
    }

    /**
     * Set fromUser
     *
     * @param User $fromUser
     * @return Notification
     */
    public function setFromPage(User $fromPage = null)
    {
        if ($fromPage->addNotificationOutgoing($this)) {
            $this->fromPage = $fromPage;
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
     * Set post
     *
     * @param Post $post
     * @return Notification
     */
    public function setPost(Post $post = null)
    {
        $this->post = $post;
    
        return $this;
    }

    /**
     * Get post
     *
     * @return Posst 
     */
    public function getPost()
    {
        return $this->post;
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
     * Get relatedObject
     *
     * @return Request
     */
    public function getRelatedObject()
    {
        if (!$this->relatedObject) {
            // $this->relatedObject = get_object($this->getPost()->getObject(), $this->getPost()->getObjectIds());
        }
        return $this->relatedObject;
    }
}