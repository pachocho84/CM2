<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page
 *
 * @ORM\Entity(repositoryClass="PageRepository")
 * @ORM\Table(name="page")
 */
class Page
{
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\Timestampable\Timestampable;
    use \CM\CMBundle\Model\ImageTrait;
    use \CM\CMBundle\Model\CoverImageTrait;
    use \CM\CMBundle\Model\BackgroundImageTrait;

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
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @ORM\Column(name="creator_id", type="integer")
     */
    private $creatorId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $creator;
        
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="page", cascade={"persist", "remove"})
     */
    private $posts;

    /**
     * @ORM\Column(name="biography_id", type="integer", nullable=true)
     */
    private $biographyId;
    
    /**
     * @ORM\OneToOne(targetEntity="Biography", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="biography_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     * @Assert\Valid
     */
    private $biography;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=150, nullable=false)
     */
    private $website;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     */
    private $vip = false;
        
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="page", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="PageUser", mappedBy="page", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", indexBy="userId", orphanRemoval=true)
     */
    private $pageUsers;

    /**
     * @ORM\OneToMany(targetEntity="PageTag", mappedBy="page", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    protected $pageTags;
    
    /**
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="page", cascade={"persist", "remove"})
     */
    private $notificationsOutgoing;
    
    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="page", cascade={"persist", "remove"})
     */
    private $requests;
    
    /**
     * @ORM\OneToMany(targetEntity="Fan", mappedBy="page", cascade={"persist", "remove"})
     */
    private $fans;
    
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->pagesUsers = new ArrayCollection();
        $this->pageTags = new ArrayCollection();
        $this->notificationsIncoming = new ArrayCollection;
        $this->notificationsOutgoing = new ArrayCollection;
        $this->requests = new ArrayCollection;
    }
    
    public function __toString()
    {
        return $this->getName();
    }

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
     * Set name
     *
     * @param string $name
     * @return Page
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
     * Get entity
     *
     * @return User 
     */
    public function getCreatorId()
    {
        return $this->creatorId;
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
        if (!is_null($creator)) {
            $this->creatorId = $creator->getId();
        }
    
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
    public function addUser(
        User $user,
        $admin = false,
        $status = PageUser::STATUS_PENDING,
        $joinEvent = PageUser::JOIN_REQUEST,
        $joinDisc = PageUser::JOIN_REQUEST,
        $joinArticle = PageUser::JOIN_REQUEST,
        $notification = true,
        $tags = null,
        $index = null
    )
    {
        if (is_null($tags)) {
            $tags = $user->getTags();
        }
        $pageUser = new PageUser;
        $pageUser->setPage($this)
            ->setUser($user)
            ->setAdmin($admin)
            ->setStatus($status)
            ->setJoinEvent($joinEvent)
            ->setJoinDisc($joinDisc)
            ->setJoinArticle($joinArticle)
            ->setNotification($notification);
        foreach ($tags as $order => $tag) {
            $pageUser->addTag($tag, $order);
        }
        $this->pageUsers[is_null($index) ? $user->getId() : $index] = $pageUser;
    
        return $this;
    }

    public function getPageUser($userId)
    {
        return $this->pageUsers[$userId];
    }

    public function addPageUser(PageUser $pageUser)
    {
        if (!$this->pageUsers->contains($pageUser)) {
            $pageUser->setPage($this);
            $this->pageUsers[$pageUsers->getUserId()] = $pageUser;
        }

        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removePageUser(PageUser $pageUser)
    {
        $this->pageUsers->removeElement($pageUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPageUsers()
    {
        return $this->pageUsers;
    }

    public function getPost()
    {
        foreach ($this->posts as $post) {
            if ($post->getType() == Post::TYPE_CREATION) {
                return $post;
            }
        }
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setPage($this);
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
     * Get entity
     *
     * @return User 
     */
    public function setBiographyId()
    {
        return $this->biographyId;
    }

    /**
     * Set biography
     *
     * @param string $biography
     * @return Page
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;
        if (!is_null($biography)) {
            $this->biographyId = $biography->getId();
        }
    
        return $this;
    }

    /**
     * Get biography
     *
     * @return string 
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * Set website
     *
     * @param string $website
     * @return Page
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    
        return $this;
    }

    /**
     * Get website
     *
     * @return string 
     */
    public function getWebsite()
    {
        return $this->website;
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
     * @param \CM\CMBundle\Entity\EntityPage $comment
     * @return Entity
     */
    public function addTag(
        Tag $tag,
        $order = null
    )
    {
        if (!$tag->isPage()) return;

        foreach ($this->pageTags as $key => $pageTag) {
            if ($pageTag->getTagId() == $tag->getId()) {
                return;
            }
        }
        $pageTag = new PageTag;
        $pageTag->setPage($this)
            ->setTag($tag)
            ->setOrder($order);
        $this->pageTags[] = $pageTag;
    
        return $this;
    }

    public function setPageTags($pageTags = array())
    {
        $this->clearTags();
        foreach ($pageTags as $order => $pageTag) {
            $pageTag->setPage($this)
                ->setOrder($order);
            $this->pageTags[] = $pageTag;
        }

        return $this;
    }

    public function addPageTag($pageTag)
    {
        if (!$this->pageTags->contains($pageTag)) {
            $pageTag->setPage($this);
            $this->pageTags[] = $pageTag;
        }

        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityPage $pages
     */
    public function removePageTag(PageTag $pageTag)
    {
        $this->pageTags->removeElement($pageTag);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function clearTags()
    {
        return $this->pageTags->clear();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPageTags()
    {
        return $this->pageTags;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTags()
    {
        $tags = array();
        foreach ($this->pageTags as $pageTag) {
            $tags[] = $pageTag->getTag();
        }
        return $tags;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTagsIds()
    {
        $tags = array();
        foreach ($this->pageTags as $pageTag) {
            $tags[] = $pageTag->getTagId();
        }
        return $tags;
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
     * Add notificationOutcoming
     *
     * @param NotificationOutcoming $notificationOutcoming
     * @return Post
     */
    public function addNotificationOutgoing(Notification $notificationOutgoing)
    {
        if (!$this->notificationOutgoing->contains($notificationOutgoing)) {
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
     * Add request
     *
     * @param Request $request
     * @return Post
     */
    public function addRequest(Request $request)
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            return true;
        }
    
        return false;
    }

    /**
     * Remove requests
     *
     * @param Request $request
     */
    public function removeRequest(Request $request)
    {
        $this->requests->removeElement($request);
    }

    /**
     * Get request
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
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
            $fan->setPage($this);
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
