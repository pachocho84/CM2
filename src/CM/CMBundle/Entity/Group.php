<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Group
 *
 * @ORM\Entity(repositoryClass="GroupRepository")
 * @ORM\Table(name="`group`")
 * @ORM\HasLifecycleCallbacks
 */
class Group
{
    use ORMBehaviors\Sluggable\Sluggable;
    use \CM\CMBundle\Model\ImageAndCoverTrait;
    
    const TYPE_DUO = 0;
    
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
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="GroupUser", mappedBy="group", cascade={"persist", "remove"})
     */
    private $groupUsers;
        
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="group", cascade={"persist", "remove"})
	 */
	private $posts;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=false)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     */
    private $vip = false;
        
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="group", cascade={"persist", "remove"})
     */
    private $images;
	
	/**
	 * @ORM\OneToMany(targetEntity="Notification", mappedBy="fromGroup", cascade={"persist", "remove"})
	 */
	private $notificationsOutgoing;
	
	/**
	 * @ORM\OneToMany(targetEntity="Request", mappedBy="fromGroup", cascade={"persist", "remove"})
	 */
	private $requestsOutgoing;
    
    /**
     * @ORM\OneToMany(targetEntity="Fan", mappedBy="group", cascade={"persist", "remove"})
     */
    private $fans;
    
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->images = new ArrayCollection();
		$this->notificationsIncoming = new ArrayCollection;
		$this->notificationsOutgoing = new ArrayCollection;
		$this->requestsIncoming = new ArrayCollection;
		$this->requestsOutgoing = new ArrayCollection;
	}
	
	public function __toString()
	{
    	return $this->getName();
	}

    protected function getRootDir()
    {
        return __DIR__.'/../Resources/public/';
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
     * Set typeId
     *
     * @param integer $typeId
     * @return Group
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Group
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setCreator(User $creator = null)
    {
        $this->creator = $creator;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $comment
     * @return Entity
     */
    public function addGroupUser(
        User $user,
        $admin = false,
        $joinEvent = GroupUser::JOIN_REQUEST,
        $joinDisc = GroupUser::JOIN_REQUEST,
        $joinArticle = GroupUser::JOIN_REQUEST,
        $notification = true,
        $userTags = array()
    )
    {
        $groupUser = new GroupUser;
        $groupUser->setGroup($this)
            ->setUser($user)
            ->setAdmin($admin)
            ->setJoinEvent($joinEvent)
            ->setJoinDisc($joinDisc)
            ->setJoinArticle($joinArticle)
            ->addUserTags($userTags)
            ->setNotification($notification);
        $this->groupUsers[] = $groupUser;
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removeGroupUser(GroupUser $groupUser)
    {
        $this->groupUsers->removeElement($groupUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroupUsers()
    {
        return $this->groupUsers;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setGroup($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     */
    public function removePost(Post $post)
    {
        $this->posts->removeElement($post);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Group
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set vip
     *
     * @param boolean $vip
     * @return Group
     */
    public function setVip($vip)
    {
        $this->vip = $vip;
    
        return $this;
    }

    /**
     * Get vip
     *
     * @return boolean 
     */
    public function getVip()
    {
        return $this->vip;
    }

    /**
     * @param User $comment
     * @return Entity
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->addUserGroup($this);
        }
    
        return $this;
    }

    /**
     * @param User $users
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $comment
     * @return Entity
     */
    public function addImage(Image $image)
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param Image $images
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getImages()
    {
        return $this->images;
    }

	/**
	 * Add notificationIncoming
	 *
	 * @param NotificationIncoming $notificationIncoming
	 * @return Post
	 */
	public function addNotificationIncoming(Notification $notificationIncoming)
	{
        if ($this->notificationsIncoming->contains($notificationIncoming)) {
	        $this->notificationsIncoming[] = $notificationIncoming;
	        return true;
	    }
	
	    return false;
	}

	/**
	 * Remove notificationsIncoming
	 *
	 * @param NotificationIncoming $notificationIncoming
	 */
	public function removeNotificationIncoming(Notification $notificationIncoming)
	{
	    $this->notificationsIncoming->removeElement($notificationIncoming);
	}

	/**
	 * Get notificationIncoming
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getNotificationsIncoming()
	{
	    return $this->notificationsIncoming;
	}

	/**
	 * Add notificationOutcoming
	 *
	 * @param NotificationOutcoming $notificationOutcoming
	 * @return Post
	 */
	public function addNotificationOutgoing(Notification $notificationOutgoing)
	{
        if ($this->notificationOutgoing->contains($notificationOutgoing)) {
	        $this->notificationOutgoing[] = $notificationOutgoing;
	        return true;
	    }
	
	    return false;
	}

	/**
	 * Remove notificationsOutcoming
	 *
	 * @param NotificationOutcoming $notificationOutcoming
	 */
	public function removeNotificationOutgoing(Notification $notificationOutcoming)
	{
	    $this->notificationOutcoming->removeElement($notificationOutcoming);
	}

	/**
	 * Get notificationOutcoming
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getNotificationsOutgoing()
	{
	    return $this->notificationOutcoming;
	}

	/**
	 * Add requestIncoming
	 *
	 * @param RequestIncoming $requestIncoming
	 * @return Post
	 */
	public function addRequestIncoming(Request $requestIncoming)
	{
        if ($this->requestsIncoming->contains($requestIncoming)) {
	        $this->requestsIncoming[] = $requestIncoming;
	        return true;
	    }
	
	    return false;
	}

	/**
	 * Remove requestsIncoming
	 *
	 * @param RequestIncoming $requestIncoming
	 */
	public function removeRequestIncoming(Request $requestIncoming)
	{
	    $this->requestsIncoming->removeElement($requestIncoming);
	}

	/**
	 * Get requestIncoming
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getRequestsIncoming()
	{
	    return $this->requestsIncoming;
	}

	/**
	 * Add requestOutcoming
	 *
	 * @param RequestOutcoming $requestOutcoming
	 * @return Post
	 */
	public function addRequestOutgoing(Request $requestOutgoing)
	{
        if ($this->requestsOutgoing->contains($requestOutgoing)) {
	        $this->requestsOutgoing[] = $requestOutgoing;
	        return true;
	    }
	
	    return false;
	}

	/**
	 * Remove requestsOutcoming
	 *
	 * @param RequestOutcoming $requestOutcoming
	 */
	public function removeRequestOutgoing(Request $requestOutgoing)
	{
	    $this->requestsOutcoming->removeElement($requestOutgoing);
	}

	/**
	 * Get requestOutcoming
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getRequestsOutgoing()
	{
	    return $this->$requestOutgoing;
	}

    /**
     * Add requestOutcoming
     *
     * @param RequestOutcoming $requestOutcoming
     * @return Post
     */
    public function addFan(Fan $fan)
    {
        if (!$this->fans->contains($fan)) {
            $this->fans[] = $fan;
            $fan->setGroup($this);
        }
    
        return $this;
    }

    /**
     * Remove requestsOutcoming
     *
     * @param RequestOutcoming $requestOutcoming
     */
    public function removeFan(Fan $fan)
    {
        $this->fans->removeElement($fan);
    }

    /**
     * Get requestOutcoming
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFans()
    {
        return $this->$fans;
    }

    /**
     * Get sluggable fields
     * 
     * @access public
     * @return void
     */
    public function getSluggableFields()
    {
        return ['name'];
    }
}