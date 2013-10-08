<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;
use CM\UserBundle\Entity\User;

/**
 * Group
 *
 * @ORM\Entity
 * @ORM\Table(name="`group`")
 */
class Group
{
    use ORMBehaviors\Sluggable\Sluggable;
    
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
     * @ORM\Column(name="type_id", type="smallint", nullable=false)
     */
    private $typeId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=150)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="CM\UserBundle\Entity\User", mappedBy="groups")
     */
    private $users;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=250, nullable=false)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=100)
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="img_offset", type="smallint")
     */
    private $imgOffset;

    /**
     * @var string
     *
     * @ORM\Column(name="cover_img", type="string", length=100)
     */
    private $coverImg;

    /**
     * @var integer
     *
     * @ORM\Column(name="cover_img_offset", type="smallint")
     */
    private $coverImgOffset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vip", type="boolean")
     */
    private $vip = false;
        
    /**
     * @ORM\OneToMany(targetEntity="CM\CMBundle\Entity\Image", mappedBy="group", cascade={"persist", "remove"})
	 */
	private $images;
    
    public function __construct()
    {
    	$this->users = new ArrayCollection();
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
     * @return Group
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
     * @return Group
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
     * Set description
     *
     * @param string $description
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * @return Group
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
     * Set vip
     *
     * @param boolean $vip
     * @return Group
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
     * Set user
     *
     * @param User $user
     * @return Group
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
     * Get sluggable fields
     * 
     * @access public
     * @return void
     */
    public function getSluggableFields()
    {
        return ['name'];
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
}