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
     * @ORM\ManyToMany(targetEntity="CM\UserBundle\Entity\User", mappedBy="entities")
     */
    private $users;
    
    /**
     * @ORM\OneToMany(targetEntity="Request", mappedBy="entity", cascade={"persist", "remove"})
     */
    private $requests;
    
    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="entity", cascade={"persist", "remove"})
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="EntityCategory", inversedBy="entities")
     * @ORM\JoinColumn(name="entity_category_id", referencedColumnName="id")
     */
    private $entityCategory;
    
    public function __construct()
    {
    	$this->users = new ArrayCollection();
    	$this->requests = new ArrayCollection();
    	$this->images = new ArrayCollection();
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
     * @param \CM\CMBundle\Entity\User $comment
     * @return Entity
     */
    public function addUser(User $user)
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\User $users
     */
    public function removeUser(User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param \CM\CMBundle\Entity\Request $requests
     * @return Entity
     */
    public function addRequest(Request $request)
    {
        if (!$this->requests->contains($request)) {
            $this->requests[] = $request;
            $request->setEntity($this);
        }
    
        return $this;
    }

    /**
     * @param \CM\CMBundle\Entity\Request $requests
     */
    public function removeRequest(Request $request)
    {
        $this->requests->removeElement($request);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRequests()
    {
        return $this->requests;
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
}
