<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Post
 *
 * @ORM\Entity(repositoryClass="PostRepository")
 * @ORM\Table(name="post")
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
    private $type = self::TYPE_CREATION;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $creator;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="posts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $entity;

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
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="posts")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $group;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="posts")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $page;
    
    /**
     * @ORM\OneToMany(targetEntity="Comment", mappedBy="post", cascade={"persist", "remove"})
     */
    private $comments;
    
    /**
     * @ORM\OneToMany(targetEntity="Like", mappedBy="post", cascade={"persist", "remove"})
     */
    private $likes;
        
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new ArrayCollection;
        $this->likes = new ArrayCollection;
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
    
    public function getPublisher()
    {
		if ($this->getPage()) {
			return $this->getPage();
		} elseif ($this->getGroup()) {
			return $this->getGroup();
		} else {
			return $this->getUser();
		}
    }
    
    public function getPublisherRoute()
    {
		if ($this->getPage()) {
			return 'page_show';
		} elseif ($this->getGroup()) {
			return 'group_show';
		} else {
			return 'user_show';
		}
    }

	public function getPublisherSex($type = 'he')
    {
		if ($this->getPageId() || $this->getGroupId()) {
			$sex = array('he' => 'it', 'his' => 'its', 'M' => '');
		} elseif (!$this->getUser()->getSex() || $this->getUser()->getSex() == 'M') {
			$sex = array('he' => 'he', 'his' => 'his', 'M' => 'M');
		} else {
			$sex = array('he' => 'she', 'his' => 'her', 'M' => 'F');
		}
		return $sex[$type];
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
     * Set entity
     *
     * @param \CM\CMBundle\Entity\Event $entity
     * @return Post
     */
    public function setEntity(Entity $entity)
    {
        $this->entity = $entity;
        $this->object = get_class($entity);
        $this->objectIds[] = $entity->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return \CM\CMBundle\Entity\Event 
     */
    public function getEntity()
    {
        return $this->entity;
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
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setGroup(Group $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setPage(Page $page = null)
    {
        $this->page = $page;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getPage()
    {
        return $this->page;
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
    
        $likes = new ArrayCollection;
        foreach ($this->getLikes() as $like) {
            if ($like->getUser() != $user) {
                $likes[] = $like;
            }
        }
        return $likes;
    }
    
    public function getUserLikesIt($user, $authenticated)
    {
        if (is_null($user) || !$authenticated) {
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
}