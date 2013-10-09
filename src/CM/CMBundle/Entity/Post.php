<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use CM\UserBundle\Entity\User;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\PostRepository")
 */
class Post
{
    use ORMBehaviors\Timestampable\Timestampable;
    
    const TYPE_CREATION = 0;

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
     * @ORM\Column(name="type", type="smallint")
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="posts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)	
     */
    private $event;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=50)
     */
    private $object;

    /**
     * @var array
     *
     * @ORM\Column(name="object_ids", type="simple_array")
     */
    private $objectIds;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)	
     */
    private $user;
	
	/**
	 * @ORM\OneToMany(targetEntity="Comment", mappedBy="post", cascade={"persist", "remove"})
	 */
	private $comments;
	
	/**
	 * @ORM\OneToMany(targetEntity="Like", mappedBy="post", cascade={"persist", "remove"})
	 */
	private $likes;
	
	/**
	 * @ORM\OneToMany(targetEntity="Notification", mappedBy="post", cascade={"persist", "remove"})
	 */
	private $notifications;
        
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->comments = new ArrayCollection;
    	$this->likes = new ArrayCollection;
    	$this->notifications = new ArrayCollection;
    }

    public function __toString()
    {
    	return "Post";
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
     * Set object
     *
     * @param string $object
     * @return Post
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
     * Set objectIds
     *
     * @param array $objectIds
     * @return Post
     */
    public function setObjectIds($objectIds)
    {
        $this->objectIds = $objectIds;
    
        return $this;
    }

    /**
     * Get objectIds
     *
     * @return array 
     */
    public function getObjectIds()
    {
        return $this->objectIds;
    }

    /**
     * Set userId
     *
     * @param integer $userId
     * @return Post
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    
        return $this;
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
     * @param Entity $entity
     * @return Image
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set event
     *
     * @param \CM\CMBundle\Entity\Event $event
     * @return Post
     */
    public function setEvent(\CM\CMBundle\Entity\Event $event = null)
    {
        $this->event = $event;
        $this->object = get_class($event);
        $this->objectIds[] = $event->getId();
    
        return $this;
    }

    /**
     * Get event
     *
     * @return \CM\CMBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     * @return Post
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;
        $comment->setPost($this);
    
        return $this;
    }

    /**
     * Remove comments
     *
     * @param \CM\CMBundle\Entity\Post $posts
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
    }

	/**
	 * Add like
	 *
	 * @param Like $like
	 * @return Post
	 */
	public function addLike(Like $like)
	{
	    $this->likes[] = $like;
	    $like->setPost($this);
	
	    return $this;
	}

	/**
	 * Remove likes
	 *
	 * @param Like $like
	 */
	public function removeLike(Like $like)
	{
	    $this->likes->removeElement($like);
	}

	/**
	 * Get like
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getLikes()
	{
	    return $this->likes;
	}
	
	public function getLikesWithoutUser($user)
    {
	    if (is_null($user)) {
    	    return $this->getLikes();
	    }
	
	    $likes = $this->getLikes();
		foreach ($this->getLikes() as $like) {
            if ($like->getUser() == $user) {
                $likes->removeElement($like);
            }
        }
		return $likes;
	}
	
	public function getUserLikeIt($user)
	{
	    if (is_null($user)) {
    	    return false;
	    }
	    
		foreach ($this->getLikes() as $like) {
            if ($like->getUser() == $user) {
                return true;
            }
        }
        
        return false;
	}
  
	public function getWhoLikesIt($user, $authenticated)
	{
		if (!is_null($user) && $authenticated) {
    		$count = 0;
    		foreach ($this->getLikes() as $like) {
        		if ($like->getUser() != $user) {
            		$count++;
        		}
    		};
			return $count;
        }
        
        return $this->getLikes()->count();
    }

	/**
	 * Add notification
	 *
	 * @param Notification $notification
	 * @return Post
	 */
	public function addNotification(Notification $notification)
	{
	    $this->notifications[] = $notification;
	    $notification->setPost($this);
	
	    return $this;
	}

	/**
	 * Remove notifications
	 *
	 * @param Notification $notification
	 */
	public function removeNotification(Notification $notification)
	{
	    $this->notifications->removeElement($notification);
	}

	/**
	 * Get notification
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getNotifications()
	{
	    return $this->notifications;
	}
}