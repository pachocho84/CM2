<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use FOS\UserBundle\Model\User as BaseUser;
use FOS\MessageBundle\Model\ParticipantInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="user")
 */
class User extends BaseUser implements ParticipantInterface
{
    use \CM\CMBundle\Model\ImageTrait;
    use \CM\CMBundle\Model\CoverImageTrait;

    const SEX_M = true;
    const SEX_F = false;

    const BIRTHDATE_VISIBLE = 0;
    const BIRTHDATE_NO_YEAR = 1;
    const BIRTHDATE_INVISIBLE = 2;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z]/",
     *     message="Your username must begin with a letter.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^[^\w\d\_\-]+$/",
     *     match=false,
     *     message="Your username must contain only letters, numbers, '_' or '-' characters.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *     pattern="/^home|homepage|index|articles|links|contacts|groups|pages$/i",
     *     match=false,
     *     message="Your username contains a reserved word.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Regex(
     *     pattern="/user|utent|event|disc|ufficio|office|press|stampa|concert|master|corso|course|accadem|scuol|school|accademi|academy|conservator|societ|associa|social/i",
     *     match=false,
     *     message="Your username contains a reserved word.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Length(
     *     min=5,
     *     max=20,
     *     minMessage="The username is too short.",
     *     maxMessage="The username is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $username;
    
    /**
     * @Assert\Length(
     *     min=10,
     *     max=50,
     *     minMessage="The username is too short.",
     *     maxMessage="The username is too long.",
     *     groups={"Registration", "Profile"}
     * )
     * @Assert\Email(
     *     message="The email '{{ value }}' is not a valid email.",
     *     checkHost=true,
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $email;
    
    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=50, nullable=false)
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=2,
     *     max=50,
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
     *     max=50,
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
     * @ORM\Column(name="birth_date_visible", type="smallint")
     * @Assert\Choice(choices = {
     *     CM\CMBundle\Entity\User::BIRTHDATE_VISIBLE,
     *     CM\CMBundle\Entity\User::BIRTHDATE_NO_YEAR,
     *     CM\CMBundle\Entity\User::BIRTHDATE_INVISIBLE
     * })
     */
    private $birthDateVisible;

    /**
     * @var string
     *
     * @ORM\Column(name="city_birth", type="string", length=50)
     * @Assert\NotBlank
     */
    private $cityBirth; // TODO: city validation

    /**
     * @var string
     *
     * @ORM\Column(name="city_current", type="string", length=50)
     * @Assert\NotBlank
     */
    private $cityCurrent; // TODO: city validation

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
     * @ORM\Column(name="newsletter", type="boolean")
     * @Assert\Choice(choices = {true, false})
     */
    private $newsletter = true;

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
     * @ORM\OneToMany(targetEntity="UserTag", mappedBy="user", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $userTags;

    /**
     * @ORM\OneToMany(targetEntity="EntityUser", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $userEntities;

    /**
     * @ORM\OneToMany(targetEntity="PageUser", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $userPages;
        
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="user", cascade={"persist", "remove"})
     */
    private $posts;
        
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="user", cascade={"persist", "remove"})
     */
    private $images;
        
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="user", cascade={"persist", "remove"})
     */
    private $comments;
    
    /**
     * @ORM\OneToMany(targetEntity="Like", mappedBy="user", cascade={"persist", "remove"})
     */
    private $likes;
        
    /**
     * @ORM\OneToMany(targetEntity="Work", mappedBy="user", cascade={"persist", "remove"})
     */
    private $works;
        
    /**
     * @ORM\OneToMany(targetEntity="Education", mappedBy="user", cascade={"persist", "remove"})
     */
    private $educations;
    
    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user", cascade={"persist", "remove"})
     */
    private $notificationsIncoming;
    
    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="fromUser", cascade={"persist", "remove"})
     */
    private $notificationsOutgoing;
    
    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="user", cascade={"persist", "remove"})
     */
    private $requestsIncoming;
    
    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="fromUser", cascade={"persist", "remove"})
     */
    private $requestsOutgoing;
    
    /**
     * @ORM\OneToMany(targetEntity="Fan", mappedBy="fromUser", cascade={"persist", "remove"})
     */
    private $fanOf;
    
    /**
     * @ORM\OneToMany(targetEntity="Fan", mappedBy="user", cascade={"persist", "remove"})
     */
    private $fans;
    
    /**
     * @ORM\OneToMany(targetEntity="Relation", mappedBy="user", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $relationsIncoming;
    
    /**
     * @ORM\OneToMany(targetEntity="Relation", mappedBy="fromUser", cascade={"persist", "remove"}, fetch="EXTRA_LAZY")
     */
    private $relationsOutgoing;

    public function __construct()
    {
        parent::__construct();
        
        $this->roles = array('ROLE_USER');
        
        $this->userTags = new ArrayCollection;
        $this->userEntities = new ArrayCollection;
        $this->userPages = new ArrayCollection;
        $this->posts = new ArrayCollection;
        $this->images = new ArrayCollection;
        $this->comments = new ArrayCollection;
        $this->likes = new ArrayCollection;
        $this->works = new ArrayCollection;
        $this->educations = new ArrayCollection;
        $this->notificationsIncoming = new ArrayCollection;
        $this->notificationsOutgoing = new ArrayCollection;
        $this->requestsIncoming = new ArrayCollection;
        $this->requestsOutgoing = new ArrayCollection;
        $this->fanOf = new ArrayCollection;
        $this->fans = new ArrayCollection;
    }
    
    public function __toString()
    {
        return $this->getFullname();
    }

    public static function className()
    {
        return get_class();
    }

    public function getFullname()
    {
        return $this->getFirstName()." ".$this->getLastName();
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
    
    public function getSlug()
    {
        return $this->getUsernameCanonical();
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

    public function getSexArray($type)
    {
        if ($this->getSex() == User::SEX_M) {
            $sex = array('he' => 'he', 'his' => 'his', 'M' => 'M');
        } else {
            $sex = array('he' => 'she', 'his' => 'her', 'M' => 'F');
        }

        return $sex[$type];
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
        return $this->sex;
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
    public function addTag(
        Tag $tag,
        $order = null
    )
    {
        foreach ($this->userTags as $key => $userTag) {
            if ($userTag->getTagId() == $tag->getId()) {
                return;
            }
        }
        $userTag = new UserTag;
        $userTag->setUser($this)
            ->setTag($tag)
            ->setOrder($order);
        $this->userTags[] = $userTag;
    
        return $this;
    }

    public function setUserTags($userTags = array())
    {
        $this->clearTags();
        foreach ($userTags as $order => $userTag) {
            $userTag->setUser($this)
                ->setOrder($order);
            $this->userTags[] = $userTag;
        }

        return $this;
    }

    public function addUserTag($userTag)
    {
        if (!$this->userTags->contains($userTag)) {
            $userTag->setUser($this);
            $this->userTags[] = $userTag;
        }

        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removeUserTag(UserTag $userTag)
    {
        $this->userTags->removeElement($userTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function clearTags()
    {
        return $this->userTags->clear();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserTags()
    {
        return $this->userTags;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        $tags = array();
        foreach ($this->userTags as $userTag) {
            $tags[] = $userTag->getTag();
        }
        return $tags;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagsIds()
    {
        $tags = array();
        foreach ($this->userTags as $userTag) {
            $tags[] = $userTag->getTagId();
        }
        return $tags;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $comment
     * @return Entity
     */
    public function addUserEntity(EntityUser $entityUser)
    {
        if (!$this->userEntities->contains($entityUser)) {
            $this->userEntities[] = $entityUser;
            $entityUser->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removeUserEntity(EntityUser $entityUser)
    {
        $this->userEntities->removeElement($entityUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserEntities()
    {
        return $this->userEntities;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $comment
     * @return Entity
     */
    public function addUserPage(PageUser $pageUser)
    {
        if (!$this->userPages->contains($pageUser)) {
            $this->userPages[] = $pageUser;
            $pageUser->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removeUserPage(PageUser $pageUser)
    {
        $this->userPages->removeElement($pageUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUserPages()
    {
        return $this->userPages;
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
            $post->setObject(get_class($this));
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
     * @param \CM\CMBundle\Entity\Work $comment
     * @return Entity
     */
    public function addWork(Work $work)
    {
        if (!$this->works->contains($work)) {
            $this->works[] = $work;
            $work->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Work $works
     */
    public function removeWork(Work $work)
    {
        $this->works->removeElement($work);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getWorks()
    {
        return $this->works;
    }

    /**
     * @param \CM\CMBundle\Entity\Education $comment
     * @return Entity
     */
    public function addEducation(Education $education)
    {
        if (!$this->educations->contains($education)) {
            $this->educations[] = $education;
            $education->setUser($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Education $educations
     */
    public function removeEducation(Education $education)
    {
        $this->educations->removeElement($education);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEducations()
    {
        return $this->educations;
    }

    /**
     * Add notificationIncoming
     *
     * @param NotificationIncoming $notificationIncoming
     * @return Post
     */
    public function addNotificationIncoming(Notification $notificationIncoming)
    {
        if (!$this->notificationsIncoming->contains($notificationIncoming)) {
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
     * Add notificationOutgoing
     *
     * @param NotificationOutcoming $notificationOutgoing
     * @return Post
     */
    public function addNotificationOutgoing(Notification $notificationOutgoing)
    {
        if (!$this->notificationsOutgoing->contains($notificationOutgoing)) {
            $this->notificationsOutgoing[] = $notificationOutgoing;
            return true;
        }
    
        return false;
    }

    /**
     * Remove notificationOutgoing
     *
     * @param NotificationOutcoming $notificationOutgoing
     */
    public function removeNotificationOutgoing(Notification $notificationOutgoing)
    {
        $this->notificationsOutgoing->removeElement($notificationOutgoing);
    }

    /**
     * Get notificationOutgoing
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotificationsOutgoing()
    {
        return $this->notificationsOutgoing;
    }

    /**
     * Add requestIncoming
     *
     * @param RequestIncoming $requestIncoming
     * @return Post
     */
    public function addRequestIncoming(Request $requestIncoming)
    {
        if (!$this->requestsIncoming->contains($requestIncoming)) {
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
        if (!$this->requestsOutgoing->contains($requestOutgoing)) {
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
        $this->requestsOutgoing->removeElement($requestOutgoing);
    }

    /**
     * Get requestOutcoming
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequestsOutgoing()
    {
        return $this->$requestsOutgoing;
    }

    /**
     * Add requestOutcoming
     *
     * @param RequestOutcoming $requestOutcoming
     * @return Post
     */
    public function addFanOf(Fan $fan)
    {
        if (!$this->fanOf->contains($fan)) {
            $this->fanOf[] = $fan;
            $fan->setFromUser($this);
        }
    
        return $this;
    }

    /**
     * Remove requestsOutcoming
     *
     * @param RequestOutcoming $requestOutcoming
     */
    public function removeFanOf(Fan $fan)
    {
        $this->fanOf->removeElement($fan);
    }

    /**
     * Get requestOutcoming
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFanOf()
    {
        return $this->$fanOf;
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
            $fan->setUser($this);
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
     * Add relationIncoming
     *
     * @param RelationIncoming $relationIncoming
     * @return Post
     */
    public function addRelationIncoming(Relation $relationIncoming)
    {
        if (!$this->relationsIncoming->contains($relationIncoming)) {
            $this->relationsIncoming[] = $relationIncoming;
            return true;
        }
    
        return false;
    }

    /**
     * Remove relationsIncoming
     *
     * @param RelationIncoming $relationIncoming
     */
    public function removeRelationIncoming(Relation $relationIncoming)
    {
        $this->relationsIncoming->removeElement($relationIncoming);
    }

    /**
     * Get relationIncoming
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelationsIncoming()
    {
        return $this->relationsIncoming;
    }

    /**
     * Add relationOutcoming
     *
     * @param RelationOutcoming $relationOutcoming
     * @return Post
     */
    public function addRelationOutgoing(Relation $relationOutgoing)
    {
        if (!$this->relationsOutgoing->contains($relationOutgoing)) {
            $this->relationsOutgoing[] = $relationOutgoing;
            return true;
        }
    
        return false;
    }

    /**
     * Remove relationsOutcoming
     *
     * @param RelationOutcoming $relationOutcoming
     */
    public function removeRelationOutgoing(Relation $relationOutgoing)
    {
        $this->relationsOutgoing->removeElement($relationOutgoing);
    }

    /**
     * Get relationOutcoming
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelationsOutgoing()
    {
        return $this->$relationsOutgoing;
    }
}
