<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="entity")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"entity"="Entity","event"="Event"})
 * @ORM\HasLifecycleCallbacks
 */
abstract class Entity
{
    use ORMBehaviors\Translatable\Translatable;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     * @Assert\Type(type="bool")
     */
    private $visible;

    /**
     * @ORM\ManyToOne(targetEntity="EntityCategory", inversedBy="entities")
     * @ORM\JoinColumn(name="entity_category_id", referencedColumnName="id")
     * @Assert\Valid
     * @Assert\NotNull
     */
    private $entityCategory;

    /**
     * @ORM\OneToMany(targetEntity="EntityUser", mappedBy="entity", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $entityUsers;
    
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="entity", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $images;
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="entity", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $posts;
    
    private $post;
    
    public function __construct()
    {
        $this->translate('en');
        $this->mergeNewTranslations();
        $this->entityUsers = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->posts = new ArrayCollection();
        $this->requests = new ArrayCollection();
    }

    /**
     * __toString function.
     * 
     * @access public
     * @return void
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
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
     * Set visible
     *
     * @param boolean $visible
     * @return Entity
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
    
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean 
     */
    public function getVisible()
    {
        return $this->visible;
    }
    
    /**
     * @param boolean $visible
     * @return Entity
     */
    public function setEntityCategory($entityCategory)
    {
        $this->entityCategory = $entityCategory;
    
        return $this;
    }

    /**
     * @return boolean 
     */
    public function getEntityCategory()
    {
        return $this->entityCategory;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $comment
     * @return Entity
     */
    public function addUser(
        User $user,
        $admin = false,
        $status = EntityUser::STATUS_PENDING,
        $notification = true,
        $userTags = null
    )
    {
        if (is_null($userTags)) {
            $userTags = $user->getUserTags();
        }
        $userTagsIds = array();
        foreach ($userTags as $userTag) {
            $userTagsIds[] = $userTag->getId();
        }
        $entityUser = new EntityUser;
        $entityUser->setEntity($this)
            ->setUser($user)
            ->setAdmin($admin)
            ->setStatus($status)
            ->addUserTags($userTagsIds)
            ->setNotification($notification);
        $this->entityUsers[] = $entityUser;
    
        return $this;
    }

    public function addEntityUser(EntityUser $entityUser)
    {
        if (!$this->entityUsers->contains($entityUser)) {
            $this->entityUsers[] = $entityUser;
            $entityUser->setEntity($this);
        }

        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function setEntityUsers(ArrayCollection $entityUser)
    {
        $this->entityUsers = $entityUsers;
    }

    /**
     * @param \CM\CMBundle\Entity\EntityUser $users
     */
    public function removeEntityUser(EntityUser $entityUser)
    {
        $this->entityUsers->removeElement($entityUser);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntityUsers()
    {
        return $this->entityUsers;
    }

    /**
     * @param \CM\CMBundle\Entity\Image $images
     * @return Entity
     */
    public function addImage(Image $image)
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setEntity($this);
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

    public function getPost()
    {
        return $this->post;
    }

    /**
     * Add posts
     *
     * @param Post $posts
     * @return Event
     */
    public function addPost(Post $post)
    {
        if ($this->getPosts()->contains($post)) {
            $this->posts[] = $post;
            $post->setEntity($this);
            
            if ($post->getType == Post::TYPE_CREATION) {
                $this->post = $post;
            }
        }
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param \CM\CMBundle\Entity\Post $posts
     */
    public function removePost(Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }
}
