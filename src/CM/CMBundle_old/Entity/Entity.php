<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection as ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity
 *
 * @ORM\Table(name="entity")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"event"="Event"})
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\EntityRepository")
 * @Gedmo\TranslationEntity(class="CM\CMBundle\Entity\EntityTranslation")
 */
class Entity
{
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
     * @ORM\Column(name="title", type="string", length=150)
     * @Gedmo\Translatable
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=150, unique=true)
     * @Gedmo\Slug(fields={"title"})
     * @Gedmo\Translatable
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=250, nullable=true)
     * @Gedmo\Translatable
     */
    private $subtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="extract", type="text", nullable=true)
     * @Gedmo\Translatable
     */
    private $extract;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text")
     * @Gedmo\Translatable
     */
    private $text;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean", nullable=true)
     */
    private $visible;

    /**
     * @ORM\OneToMany(
     *     targetEntity="EntityTranslation",
     *  mappedBy="object",
     *  cascade={"persist", "remove"}
     * )
     * @Assert\Valid(deep = true)
     */
    private $translations;

    /**
     * __construct function.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->translations = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Entity
     */
    public function setTitle($title)
    {
        $this->title = $title;
    
        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Entity
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    
        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     * @return Entity
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    
        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string 
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set extract
     *
     * @param string $extract
     * @return Entity
     */
    public function setExtract($extract)
    {
        $this->extract = $extract;
    
        return $this;
    }

    /**
     * Get extract
     *
     * @return string 
     */
    public function getExtract()
    {
        return $this->extract;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Entity
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
     * getTranslations function.
     * 
     * @access public
     * @return void
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * addTranslation function.
     * 
     * @access public
     * @param EntityTranslation $t
     * @return void
     */
    public function addTranslation(EntityTranslation $t)
    {
        if (!$this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
        
        return $this;
    }
    
    public function removeTranslation(EntityTranslation $t)
    {

        return $this;
    }
}
