<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="EntityRepository")
 * @ORM\Table(name="entity")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "entity"="Entity",
 *     "event"="Event",
 *     "disc"="Disc",
 *     "biography"="Biography",
 *     "image_album"="ImageAlbum",
 *     "multimedia"="Multimedia",
 *     "article"="Article",
 *     "link"="Link"
 * })
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
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=true)
     */
    protected $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="source", type="string", length=150, nullable=true)
     */
    protected $source;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date", nullable=true)
     */
    protected $date;

    /**
     * @ORM\ManyToOne(targetEntity="EntityCategory", inversedBy="entities")
     * @ORM\JoinColumn(name="entity_category_id", referencedColumnName="id")
     * @Assert\Valid
     * @Assert\NotNull(groups="Event")
     */
    protected $category;

    /**
     * @var integer
     *
     * @ORM\Column(name="image_id", type="integer", nullable=true)
     */
    protected $imageId;

    /**
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(name="image_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    protected $image;

    /**
     * @var integer
     *
     * @ORM\Column(name="img_offset", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $imgOffset;
    
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="entity", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $images;
    
    /**
     * @ORM\OneToMany(targetEntity="Multimedia", mappedBy="entity", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $multimedia;

    /**
     * @var integer
     *
     * @ORM\Column(name="post_id", type="integer", nullable=true)
     */
    protected $postId;

    /**
     * @ORM\ManyToOne(targetEntity="Post", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="post_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     **/
    protected $post;

    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="entity", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     */
    protected $posts;

    /**
     * @ORM\OneToMany(targetEntity="EntityUser", mappedBy="entity", cascade={"persist", "remove"}, fetch="EXTRA_LAZY", indexBy="userId", orphanRemoval=true)
     * @Assert\Valid
     */
    protected $entityUsers;
    
    public function __construct()
    {
        $this->images = new ArrayCollection;
        $this->multimedia = new ArrayCollection;
        $this->posts = new ArrayCollection;
        $this->entityUsers = new ArrayCollection;
        $this->requests = new ArrayCollection;
    }

    /**
     * __toString function.
     * 
     * @access public
     * @return void
     */
    public function __toString()
    {
        return $this->proxyCurrentLocaleTranslation('__toString');
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
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
     * Set type
     *
     * @param integer $type
     * @return Multimedia
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
     * Set source
     *
     * @param string $source
     * @return Link
     */
    public function setSource($source)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return string 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Article
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }
    
    /**
     * @param boolean $visible
     * @return Entity
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * @return boolean 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getImageId()
    {
        return $this->imageId;
    }

    /**
     * Set image
     *
     * @param Image $image
     * @return Request
     */
    public function setImage(Image $image)
    {
        $this->image = $image;
        $this->addImage($image);
        if (!is_null($image)) {
            $this->imageId = $image->getId();
        }
    
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
     * Set imgOffset
     *
     * @param integer $imgOffset
     * @return Image
     */
    public function setImgOffset($imgOffset)
    {
        $this->imgOffset = $imgOffset;
    
        return $this;
    }

    /**
     * Get imgOffset
     *
     * @return integer 
     */
    public function getImgOffset()
    {
        return $this->imgOffset;
    }

    /**
     * @param Image $images
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
     * @param Image $images
     */
    public function removeImage(Image $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * @return ArrayCollection 
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * @param Multimedia $multimedia
     * @return Entity
     */
    public function addMultimedia(Multimedia $multimedia)
    {
        if (!$this->multimedia->contains($multimedia)) {
            $this->multimedia[] = $multimedia;
            $multimedia->setEntity($this);
        }
    
        return $this;
    }

    /**
     * @param Multimedia $multimedia
     */
    public function removeMultimedia(Multimedia $multimedia)
    {
        $this->multimedia->removeElement($multimedia);
    }

    /**
     * @return ArrayCollection 
     */
    public function getMultimedia()
    {
        return $this->multimedia;
    }

    /**
     * Get userId
     *
     * @return integer 
     */
    public function getPostId()
    {
        return $this->postId;
    }

    /**
     * Set post
     *
     * @param Post $post
     * @return Request
     */
    public function setPost(Post $post)
    {
        $this->post = $post;
        $this->addPost($post);
        if (!is_null($post)) {
            $this->postId = $post->getId();
        }
    
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

    public function getLastPost()
    {
        $post = $this->posts[0];
        foreach ($this->posts as $p) {
            if ($p->getUpdatedAt() > $post->getUpdatedAt()) {
                $post = $p;
            }
        }
        return $post;
    }

    /**
     * Add posts
     *
     * @param Post $posts
     * @return Event
     */
    public function addPost(Post $post, $setObject = true)
    {
        if (!$this->getPosts()->contains($post)) {
            $this->posts[] = $post;
            $post->setEntity($this);
            if ($setObject) {
                $post->setObject(get_class($this))
                    ->setObjectIds(array($this->getId()));
            }
        }
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param Post $posts
     */
    public function removePost(Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return ArrayCollection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param EntityUser $comment
     * @return Entity
     */
    public function addUser(
        User $user,
        $admin = false,
        $status = EntityUser::STATUS_PENDING,
        $notification = true,
        $tags = null,
        $index = null
    )
    {
        if (is_null($tags)) {
            $tags = $user->getTags();
        }
        $entityUser = new EntityUser;
        $entityUser->setEntity($this)
            ->setUser($user)
            ->setAdmin($admin)
            ->setStatus($status)
            ->setNotification($notification);
        foreach ($tags as $order => $tag) {
            $entityUser->addTag($tag, $order);
        }
        $this->entityUsers[is_null($index) ? $user->getId() : $index] = $entityUser;
    
        return $this;
    }

    public function addEntityUser(EntityUser $entityUser)
    {
        if (!$this->entityUsers->contains($entityUser)) {
            $entityUser->setEntity($this);
            $this->entityUsers[$entityUser->getUserId()] = $entityUser;
        }

        return $this;
    }

    /**
     * @param EntityUser $users
     */
    public function setEntityUsers($entityUsers)
    {
        $this->entityUsers = new ArrayCollection($entityUsers);
    }

    /**
     * @param EntityUser $users
     */
    public function removeEntityUser(EntityUser $entityUser)
    {
        $this->entityUsers->removeElement($entityUser);
    }

    /**
     * @return ArrayCollection 
     */
    public function getEntityUsers()
    {
        return $this->entityUsers;
    }
}
