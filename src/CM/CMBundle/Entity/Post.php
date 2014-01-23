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
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
    use ORMBehaviors\Timestampable\Timestampable;
    
    const TYPE_CREATION = 0;
    const TYPE_REGISTRATION = 1;
    const TYPE_UPDATE = 2;
    const TYPE_FAN_USER = 3;
    const TYPE_FAN_GROUP = 4;
    const TYPE_FAN_PAGE = 5;
    const TYPE_EDUCATION = 6;

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
     * @ORM\Column(name="creator_id", type="integer")
     */
    private $creatorId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $creator;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private $userId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $user;

    /**
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     */
    private $entityId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="posts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="PostEntity", inversedBy="posts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $postEntity;

    /**
     * @var string
     *
     * @ORM\Column(name="object", type="string", length=50)
     */
    private $object;

    /**
     * @var array
     *
     * @ORM\Column(name="object_ids", type="text", nullable=true)
     */
    private $objectIds;

    private $objectIdsArray = array();

    /**
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     **/
    private $groupId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="posts")
     * @ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $group;

    /**
     * @ORM\Column(name="page_id", type="integer", nullable=true)
     **/
    private $pageId;

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
    
    public function getPublisherId()
    {
        if ($this->getPage()) {
            return $this->getPageId();
        } elseif ($this->getGroup()) {
            return $this->getGroupId();
        } else {
            return $this->getUserId();
        }
    }
    
    public function getPublisherRoute()
    {
        if ($this->getPage()) {
            return 'page';
        } elseif ($this->getGroup()) {
            return 'group';
        } else {
            return 'user';
        }
    }

    public function getPublisherSex($type = 'he')
    {
        if (!is_null($this->getPageId()) || !is_null($this->getGroupId())) {
            $sex = array('he' => 'it', 'his' => 'its', 'M' => '');
        } elseif ($this->getUser()->getSex() == User::SEX_M) {
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
     * Get userId
     *
     * @return integer 
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
        if (!is_null($user)) {
            $this->userId = $user->getId();
        }
    
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
     * Get userId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
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
        if (!is_null($entity)) {
            $this->entityId = $entity->getId();
        }
    
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
        $this->objectIdsArray = $objectIds;
        $this->objectIds = 'changed';
    
        return $this;
    }

    /**
     * Set objectIds
     *
     * @param array $objectIds
     * @return Post
     */
    public function addObjectId($objectId)
    {
        $this->removeObjectId('changed');
        $this->objectIdsArray = array_merge($this->objectIdsArray, (array)$objectId);
        $this->objectIds = 'changed';
    
        return $this;
    }

    public function removeObjectId($objectId)
    {
        if (($key = array_search($objectId, $this->objectIdsArray)) !== false) {
            unset($this->objectIdsArray[$key]);
            $this->objectIds = 'changed';
        }
    }

    /**
     * Get objectIds
     *
     * @return array 
     */
    public function getObjectIds()
    {
        return $this->objectIdsArray;
    }

    /**
     * Get fromUser
     *
     * @return User 
     */
    public function getGroupId()
    {
        return $this->groupId;
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
        if (!is_null($group)) {
            $this->groupId = $group->getId();
        }
    
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
     * Get entity
     *
     * @return Entity 
     */
    public function getPageId()
    {
        return $this->pageId;
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
        if (!is_null($page)) {
            $this->pageId = $page->getId();
        }
    
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
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }
    
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
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);            
        }
    
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

    public function getRelatedImg()
    {
        if ($this->getEntityId()) {
            echo $this->getEntity()->getImages()[0];
        } elseif ($this->getPageId()) {
            return $this->getPage()->getImg();
        } elseif ($this->getGroupId()) {
            return $this->getGroup()->getImg();
        } else {
            return $this->getUser()->getImg();
        }
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function arrayToString()
    {
        if (count($this->objectIdsArray) == 0) {
            $this->objectIds = '';
        } else {
            $this->objectIds = ',';
            foreach ($this->objectIdsArray as $id) {
                $this->objectIds .= $id.',';
            }
        }
    }

    /**
     * @ORM\PostLoad()
     */
    public function stringToArray()
    {        
        foreach (preg_split('@,@', $this->objectIds, null, PREG_SPLIT_NO_EMPTY) as $id) {
            $this->objectIdsArray[] = $id;
        }
    }
}