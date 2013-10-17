<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Page
 *
 * @ORM\Entity
 * @ORM\Table(name="page")
 * @ORM\HasLifecycleCallbacks
 */
class Page
{
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\Timestampable\Timestampable;
    use \CM\CMBundle\Model\ImageAndCoverTrait;

    const TYPE_ASSOCIATION = 0;

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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $creator;

    /**
     * @ORM\OneToMany(targetEntity="PageUser", mappedBy="page", cascade={"persist", "remove"})
     */
    private $pageUsers;
        
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="page", cascade={"persist", "remove"})
	 */
	private $posts;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=false)
     */
    private $description;

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
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Image", mappedBy="page", cascade={"persist", "remove"})
     */
    private $images;
    
    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->pagesUsers = new ArrayCollection();
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
     * Set type
     *
     * @param integer $type
     * @return Page
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
    public function addPageUser(
        User $user,
        $admin = false,
        $joinEvent = PageUser::JOIN_REQUEST,
        $joinDisc = PageUser::JOIN_REQUEST,
        $joinArticle = PageUser::JOIN_REQUEST,
        $notification = true,
        $userTags = array()
    )
    {
        $pageUser = new PageUser;
        $pageUser->setPage($this)
            ->setUser($user)
            ->setAdmin($admin)
            ->setJoinEvent($joinEvent)
            ->setJoinDisc($joinDisc)
            ->setJoinArticle($joinArticle)
            ->addUserTags($userTags)
            ->setNotification($notification);
        $this->pageUsers[] = $pageUser;
    
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

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addPost(Post $post)
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setPage($this);
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
     * @return Page
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
     * @return Page
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
