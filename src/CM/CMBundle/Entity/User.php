<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;
/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseUser
{
	use \CM\CMBundle\Model\ImageAndCoverTrait;

	const SEX_M = true;
	const SEX_F = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max="50",
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="Please enter your last name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max="50",
     *     minMessage="The last name is too short.",
     *     maxMessage="The last name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $lastName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sex", type="boolean", nullable=false)
     * @Assert\Choice(choices = {CM\CMBundle\Entity\User::SEX_M, CM\CMBundle\Entity\User::SEX_F}, message = "Choose a valid gender.")
     */
    private $sex;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birth_date", type="date", nullable=false)
     * @Assert\NotBlank
     * @Assert\Date
     */
    private $birthDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="birth_date_visible", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $birthDateVisible;

    /**
     * @var string
     *
     * @ORM\Column(name="city_birth", type="string", length=50)
     * @Assert\NotBlank
     */
    private $cityBirth;

    /**
     * @var string
     *
     * @ORM\Column(name="city_current", type="string", length=50)
     * @Assert\NotBlank
     */
    private $cityCurrent;

    /**
     * @var boolean
     *
     * @ORM\Column(name="newsletter", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $newsletter = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $vip = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="notify_email", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $notifyEmail = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="request_email", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $requestEmail = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="message_email", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $messageEmail = true;

    /**
     * @var string
     *
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    private $notes;

    /**
     * @ORM\ManyToMany(targetEntity="CM\CMBundle\Entity\Group", inversedBy="users")
     * @ORM\JoinTable(name="users_groups")
     */
	protected $groups;

    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\EntityUser", mappedBy="user")
     */
	protected $entitiesUsers;
        
    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Page", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $pages;
        
    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Post", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $posts;
        
    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Comment", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $comments;
        
    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Image", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $images;
	
	/**
	 * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Like", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $likes;
	
	/**
	 * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Notification", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $notificationsIncoming;
	
	/**
	 * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Notification", mappedBy="fromUser", cascade={"persist", "remove"})
	 */
	private $notificationsOutcoming;
	
	/**
	 * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Request", mappedBy="user", cascade={"persist", "remove"})
	 */
	private $requestsIncoming;
	
	/**
	 * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Request", mappedBy="fromUser", cascade={"persist", "remove"})
	 */
	private $requestsOutcoming;

	public function __construct()
	{
		parent::__construct();
		
		$this->entitiesUsers = new ArrayCollection;
		$this->groups = new ArrayCollection;
		$this->pages = new ArrayCollection;
		$this->posts = new ArrayCollection;
		$this->comments = new ArrayCollection;
		$this->images = new ArrayCollection;
		$this->likes = new ArrayCollection;
		$this->notificationsIncoming = new ArrayCollection;
		$this->notificationsOutcoming = new ArrayCollection;
		$this->requestsIncoming = new ArrayCollection;
		$this->requestsOutcoming = new ArrayCollection;
	}
	
	public function __toString()
	{
    	return $this->getFirstName()." ".$this->getLastName();
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
     * Set firstName
     *
     * @param string $firstName
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    
        return $this;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    
        return $this;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set sex
     *
     * @param boolean $sex
     * @return User
     */
    public function setSex($sex)
    {
        if (!in_array($sex, array(self::SEX_M, self::SEX_F))) {
            throw new \InvalidArgumentException("Invalid sex");
        }

        $this->sex = $sex;
            
        return $this;
    }

    /**
     * Get sex
     *
     * @return boolean 
     */
    public function getSex()
    {
        return $this->sex ? 'M' : 'F';
    }

    /**
     * Set birthDate
     *
     * @param \DateTime $birthDate
     * @return User
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;
    
        return $this;
    }

    /**
     * Get birthDate
     *
     * @return \DateTime 
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set birthDateVisible
     *
     * @param boolean $birthDateVisible
     * @return User
     */
    public function setBirthDateVisible($birthDateVisible)
    {
        $this->birthDateVisible = $birthDateVisible;
    
        return $this;
    }

    /**
     * Get birthDateVisible
     *
     * @return boolean 
     */
    public function getBirthDateVisible()
    {
        return $this->birthDateVisible;
    }

    /**
     * Set cityBirth
     *
     * @param string $cityBirth
     * @return User
     */
    public function setCityBirth($cityBirth)
    {
        $this->cityBirth = $cityBirth;
    
        return $this;
    }

    /**
     * Get cityBirth
     *
     * @return string 
     */
    public function getCityBirth()
    {
        return $this->cityBirth;
    }

    /**
     * Set cityCurrent
     *
     * @param string $cityCurrent
     * @return User
     */
    public function setCityCurrent($cityCurrent)
    {
        $this->cityCurrent = $cityCurrent;
    
        return $this;
    }

    /**
     * Get cityCurrent
     *
     * @return string 
     */
    public function getCityCurrent()
    {
        return $this->cityCurrent;
    }

    /**
     * Set newsletter
     *
     * @param boolean $newsletter
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;
    
        return $this;
    }

    /**
     * Get newsletter
     *
     * @return boolean 
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * Set vip
     *
     * @param boolean $vip
     * @return User
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
     * Set notifyEmail
     *
     * @param boolean $notifyEmail
     * @return User
     */
    public function setNotifyEmail($notifyEmail)
    {
        $this->notifyEmail = $notifyEmail;
    
        return $this;
    }

    /**
     * Get notifyEmail
     *
     * @return boolean 
     */
    public function getNotifyEmail()
    {
        return $this->notifyEmail;
    }

    /**
     * Set requestEmail
     *
     * @param boolean $requestEmail
     * @return User
     */
    public function setRequestEmail($requestEmail)
    {
        $this->requestEmail = $requestEmail;
    
        return $this;
    }

    /**
     * Get requestEmail
     *
     * @return boolean 
     */
    public function getRequestEmail()
    {
        return $this->requestEmail;
    }

    /**
     * Set messageEmail
     *
     * @param boolean $messageEmail
     * @return User
     */
    public function setMessageEmail($messageEmail)
    {
        $this->messageEmail = $messageEmail;
    
        return $this;
    }

    /**
     * Get messageEmail
     *
     * @return boolean 
     */
    public function getMessageEmail()
    {
        return $this->messageEmail;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return User
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    
        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $comment
     * @return Entity
     */
    public function addEntitiesUsers(EntityUser $entityUser)
    {
        if (!$this->entitiesUsers->contains($entityUser)) {
            $this->entitiesUsers[] = $entityUser;
            $entityUser->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removeEntitiesUsers(EntityUser $entityUser)
    {
        $this->entitiesUsers->removeElement($entityUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntitiesUsers()
    {
        return $this->entitiesUsers;
    }

    /**
     * @param \CM\CMBundle\Entity\Group $group
     * @return Entity
     */
    public function addUserGroup(Group $group)
    {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     */
    public function removeUserGroup(Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserGroups()
    {
        return $this->groups;
    }

    /**
     * @param \CM\CMBundle\Entity\Page $page
     * @return Entity
     */
    public function addPage(Page $page)
    {
        if (!$this->pages->contains($page)) {
            $this->pages[] = $page;
            $page->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     */
    public function removePage(Page $page)
    {
        $this->pages->removeElement($page);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setUser($this);
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
     * @param \CM\CMBundle\Entity\Comment $comment
     * @return Entity
     */
    public function addComment(Comment $comment)
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Comment $images
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getComments()
    {
        return $this->comments;
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
     * @param \CM\CMBundle\Entity\Image $images
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

	/**
	 * Add notificationIncoming
	 *
	 * @param NotificationIncoming $notificationIncoming
	 * @return Post
	 */
	public function addNotificationIncoming(Notification $notificationIncoming)
	{
	    $this->notificationsIncoming[] = $notificationIncoming;
	    $notificationIncoming->setPost($this);
	
	    return $this;
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
	public function addNotificationOutcoming(Notification $notificationOutcoming)
	{
	    $this->notificationsOutcoming[] = $notificationOutcoming;
	    $notificationOutcoming->setPost($this);
	
	    return $this;
	}

	/**
	 * Remove notificationsOutcoming
	 *
	 * @param NotificationOutcoming $notificationOutcoming
	 */
	public function removeNotificationOutcoming(Notification $notificationOutcoming)
	{
	    $this->notificationsOutcoming->removeElement($notificationOutcoming);
	}

	/**
	 * Get notificationOutcoming
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getNotificationsOutcoming()
	{
	    return $this->notificationsOutcoming;
	}

	/**
	 * Add requestIncoming
	 *
	 * @param RequestIncoming $requestIncoming
	 * @return Post
	 */
	public function addRequestIncoming(Request $requestIncoming)
	{
	    $this->requestsIncoming[] = $requestIncoming;
	    $requestIncoming->setPost($this);
	
	    return $this;
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
	public function addRequestOutcoming(Request $requestOutcoming)
	{
	    $this->requestsOutcoming[] = $requestOutcoming;
	    $requestOutcoming->setPost($this);
	
	    return $this;
	}

	/**
	 * Remove requestsOutcoming
	 *
	 * @param RequestOutcoming $requestOutcoming
	 */
	public function removeRequestOutcoming(Request $requestOutcoming)
	{
	    $this->requestsOutcoming->removeElement($requestOutcoming);
	}

	/**
	 * Get requestOutcoming
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getRequestsOutcoming()
	{
	    return $this->requestsOutcoming;
	}
}
