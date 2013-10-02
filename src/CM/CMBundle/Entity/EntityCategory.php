<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Entity\EntityCategoryEnum;

/**
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\EntityCategoryRepository")
 * @ORM\Table(name="entity_category")
 */
class EntityCategory
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
     * @var array
     *
     * @ORM\Column(name="entity_type", type="smallint")
     */
    private $entity_type;
     
    /**
     * @ORM\OneToMany(targetEntity="Entity", mappedBy="entity_category")
     */
    private $entities;
    
    public function __construct()
    {
        $this->entities = new ArrayCollection();
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function __toString()
    {
        return $this->proxyCurrentLocaleTranslation('__toString');
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
     * Set entityType
     *
     * @param array $entityType
     * @return EntityCategory
     */
    public function setEntityType($entity_type)
    {
        $this->entity_type = $entity_type;
    
        return $this;
    }

    /**
     * Get entityType
     *
     * @return array 
     */
    public function getEntityType()
    {
        return $this->entity_type;
    }

    /**
     * Add entities
     *
     * @param \CM\CMBundle\Entity\EventDate $eventDates
     * @return Event
     */
    public function addEntity(Entity $entity)
    {
        if (!$this->entities->contains($entity)) {
            $this->entities[] = $entity;
            $entity->setEntityCategory($this);
        }
    
        return $this;
    }

    /**
     * Remove entities
     *
     * @param \CM\CMBundle\Entity\EventDate $eventDates
     */
    public function removeEntity(Entity $entity)
    {
        $this->entities->removeElement($entity);
    }

    /**
     * Get entities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEntity()
    {
        return $this->entities;
    }
}
