<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use CM\UserBundle\Entity\User as User;

/**
 * Post
 *
 * @ORM\Table(name="post")
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\PostRepository")
 */
class Post
{
    use ORMBehaviors\Timestampable\Timestampable;
    
    const TYPE_CREATION = 'CREATION';

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
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="posts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)	
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="posts")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)	
     */
    private $event;

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
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)	
     */
    private $user;

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
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setEntity(Entity $entity = null)
    {
        $this->entity = $entity;
        $this->object = get_class($entity);
        $this->objectIds[] = $entity->getId();
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getEntity()
    {
    	echo get_class($this->entity);
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
     * Set event
     *
     * @param \CM\CMBundle\Entity\Event $event
     * @return Post
     */
    public function setEvent(\CM\CMBundle\Entity\Event $event = null)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return \CM\CMBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}