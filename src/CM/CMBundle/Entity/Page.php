<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use CM\UserBundle\Entity\User;

/**
 * Page
 *
 * @ORM\Table(name="page")
 * @ORM\Entity
 */
class Page
{
    use ORMBehaviors\Sluggable\Sluggable,
    	ORMBehaviors\Timestampable\Timestampable;

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
     * @ORM\Column(name="type_id", type="integer", nullable=false)
     */
    private $typeId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User", inversedBy="pages")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=100, nullable=true)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="img_offset", type="smallint", nullable=true)
     */
    private $imgOffset;

    /**
     * @var string
     *
     * @ORM\Column(name="cover_img", type="string", length=100, nullable=true)
     */
    private $coverImg;

    /**
     * @var integer
     *
     * @ORM\Column(name="cover_img_offset", type="smallint", nullable=true)
     */
    private $coverImgOffset;

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
     * Set typeId
     *
     * @param integer $typeId
     * @return Page
     */
    public function setTypeId($typeId)
    {
        $this->typeId = $typeId;
    
        return $this;
    }

    /**
     * Get typeId
     *
     * @return integer 
     */
    public function getTypeId()
    {
        return $this->typeId;
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
     * Set img
     *
     * @param string $img
     * @return Page
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
     * Set imgOffset
     *
     * @param integer $imgOffset
     * @return Page
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
     * Set coverImg
     *
     * @param string $coverImg
     * @return Page
     */
    public function setCoverImg($coverImg)
    {
        $this->coverImg = $coverImg;
    
        return $this;
    }

    /**
     * Get coverImg
     *
     * @return string 
     */
    public function getCoverImg()
    {
        return $this->coverImg;
    }

    /**
     * Set coverImgOffset
     *
     * @param integer $coverImgOffset
     * @return Page
     */
    public function setCoverImgOffset($coverImgOffset)
    {
        $this->coverImgOffset = $coverImgOffset;
    
        return $this;
    }

    /**
     * Get coverImgOffset
     *
     * @return integer 
     */
    public function getCoverImgOffset()
    {
        return $this->coverImgOffset;
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
