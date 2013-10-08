<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use CM\UserBundle\Entity\User;

/**
 * Notification
 *
 * @ORM\Entity
 * @ORM\Table(name="notification")
 */
class Notification
{
    use ORMBehaviors\Timestampable\Timestampable;
    
	const STATUS_NEW = 'new';
	const STATUS_NOTIFIED = 'notified';

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
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status = self::STATUS_NEW;

    /**
     * @ORM\ManyToOne(targetEntity="Post", inversedBy="notifications")
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $post;

    /**
     * @ORM\ManyToOne(targetEntity="Image", inversedBy="notifications")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    private $image;

    /**
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User", inversedBy="notificationsIncoming")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User", inversedBy="notificationsOutcoming")
     * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $fromUser;

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
    	if ($sex == self::STATUS_NEW) {
	    	$this->status = true;
    	}
        elseif ($sex == self::STATUS_NOTIFIED) {
	        $this->status = false;
        }
        else {
            throw new \InvalidArgumentException("Invalid status");
        }
    
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
     * @return Post 
     */
    public function getPost()
    {
        return $this->post;
    }

    /**
     * Set image
     *
     * @param Image $image
     * @return Notification
     */
    public function setImage(Image $image = null)
    {
        $this->image = $image;
    
        return $this;
    }

    /**
     * Get image
     *
     * @return Image 
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Notification
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
     * Set fromUser
     *
     * @param User $fromUser
     * @return Notification
     */
    public function setFromUser(User $fromUser = null)
    {
        $this->fromUser = $fromUser;
    
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
}