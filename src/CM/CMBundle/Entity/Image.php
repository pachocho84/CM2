<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Image
 *
 * @ORM\Entity(repositoryClass="ImageRepository")
 * @ORM\Table(name="image")
 * @ORM\HasLifecycleCallbacks
 */
class Image
{
    use ORMBehaviors\Timestampable\Timestampable;
    use \CM\CMBundle\Model\ImageTrait;
        
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     **/
    private $entityId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="images")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $entity;

    /**
     * @ORM\Column(name="user_id", type="integer")
     **/
    private $userId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="images")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $user;

    /**
     * @ORM\Column(name="group_id", type="integer", nullable=true)
     **/
    private $groupId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="images")
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
     * @ORM\ManyToOne(targetEntity="Page", inversedBy="images")
     * @ORM\JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $page;

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
    
    public function __construct()
    {
        $this->likes = new ArrayCollection();
    }

    public function __toString()
    {
      return $this->img;
    }

    public static function className()
    {
        return get_class();
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
    
    public function getPublisherType()
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
     * Get entityId
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
     * @param Entity $entity
     * @return Image
     */
    public function setEntity(Entity $entity = null)
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
     * @return Entity 
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getUserId()
    {
        return $this->userId;
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
        if (!is_null($user)) {
            $this->userId = $user->getId();
        }
    
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
     * Get fromUser
     *
     * @return User 
     */
    public function getGroupId()
    {
        return $this->groupId;
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
        if (!is_null($group)) {
            $this->groupId = $group->getId();
        }
    
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
     * Get fromUser
     *
     * @return User 
     */
    public function getPageId()
    {
        return $this->pageId;
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
        if (!is_null($page)) {
            $this->pageId = $page->getId();
        }
    
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
}
