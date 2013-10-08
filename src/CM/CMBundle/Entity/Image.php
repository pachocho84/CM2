<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CM\UserBundle\Entity\User;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\ImageRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    use ORMBehaviors\Timestampable\Timestampable;
        
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
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="images")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)	
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User", inversedBy="images")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)	
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="images")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)	
     */
    private $group;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="images")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)	
     */
    private $page;
    
    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=100)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="offset", type="smallint", nullable=true)
     */
    private $offset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="main", type="boolean", nullable=true)
     */
    private $main;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="smallint", nullable=true)
     */
    private $sequence;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;
	
	/**
	 * @ORM\OneToMany(targetEntity="Like", mappedBy="image", cascade={"persist", "remove"})
	 */
	private $likes;
	
	/**
	 * @ORM\OneToMany(targetEntity="Comment", mappedBy="image", cascade={"persist", "remove"})
	 */
	private $comments;
	
	/**
	 * @ORM\OneToMany(targetEntity="Notification", mappedBy="image", cascade={"persist", "remove"})
	 */
	private $notifications;
    
    public function __construct()
    {
    	$this->likes = new ArrayCollection();
    	$this->notifications = new ArrayCollection();
    }

    /**
     * @Assert\Image(
     *     minWidth = 250,
     *     maxWidth = 750,
     *     minHeight = 250,
     *     maxHeight = 750,
     *     maxSize = "8M",
     *     mimeTypes = {"image/png", "image/jpeg", }
     * )
     */
    private $file;

	public function __toString()
	{
		return $this->img;
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
     * Set entityId
     *
     * @param integer $entityId
     * @return Image
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    
        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return Image
     */
    public function setImg($img)
    {
        $this->img = $img;
    
        return $this;
    }

    /**
     * Get img
     *
     * @return string 
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set offset
     *
     * @param integer $offset
     * @return Image
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    
        return $this;
    }

    /**
     * Get offset
     *
     * @return integer 
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set main
     *
     * @param boolean $main
     * @return Image
     */
    public function setMain($main)
    {
        $this->main = $main;
    
        return $this;
    }

    /**
     * Get main
     *
     * @return boolean 
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return Image
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    
        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Image
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setEntity(Event $entity = null)
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

    /**
     * Set user
     *
     * @param User $user
     * @return Image
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
    
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
     * Set group
     *
     * @param Group $group
     * @return Image
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return Group 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set page
     *
     * @param Page $page
     * @return Image
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
    
        return $this;
    }

    /**
     * Get page
     *
     * @return Page 
     */
    public function getPage()
    {
        return $this->page;
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
	 * @param Comment $comment
	 */
	public function removeComment(Comment $comment)
	{
	    $this->comments->removeElement($comment);
	}

	/**
	 * Get comment
	 *
	 * @return \Doctrine\Common\Collections\Collection 
	 */
	public function getComments()
	{
	    return $this->comments;
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

    /**
     * Set file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }    

    public function getAbsolutePath()
    {
        return null === $this->img
            ? null
            : $this->getUploadRootDir().'/'.$this->img;
    }

    protected function getUploadRootDir()
    {
        // the absolute directory path where uploaded images should be saved
        return __DIR__.'/../Resources/public/'.$this->getUploadDir();
    }

    protected function getUploadDir()
    {
   		// if you change this, change it also in the config.yml file!
        return 'uploads/images/full';
    }

    /**
     * @ORM\PrePersist()
     */
    public function sanitizeFileName()
    {
        if (null !== $this->getFile()) {
        	$fileName = uniqid($this->getFile()->getClientOriginalName().time(), true);
            $this->img = $fileName.'.'.$this->getFile()->guessExtension(); // FIXME: doesn't work with bmp files
        }
    }

    /**
     * @ORM\PostPersist()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->img);

   		// clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        if ($file = $this->getAbsolutePath()) {
            unlink($file);
        }
    }
}
