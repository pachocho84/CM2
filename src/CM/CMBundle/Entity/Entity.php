<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="entity")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({"entity"="Entity","event"="Event"})
 */
class Entity
{
	use ORMBehaviors\Translatable\Translatable;
	
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
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
     */
    private $entity_category;

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
     * Set entity category
     *
     * @param boolean $visible
     * @return Entity
     */
    public function setEntityCategory($entity_category)
    {
        $this->entity_category = $entity_category;
    
        return $this;
    }

    /**
     * Get entity category
     *
     * @return boolean 
     */
    public function getEntityCategory()
    {
        return $this->entity_category;
    }
}
