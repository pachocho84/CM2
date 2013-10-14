<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Entity\EntityCategoryEnum;

/**
 * @ORM\Entity(repositoryClass="EntityCategoryRepository")
 * @ORM\Table(name="entity_category")
 */
class EntityCategory
{
    use ORMBehaviors\Translatable\Translatable;

    const ENTITY     = 0;
    const EVENT         = 1;
    const DISC         = 2;
    const ARTICLE     = 3;
    const LINK         = 4;
    const IMAGE         = 5;
    const MULTIMEDIA = 6;
    
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
     * @ORM\Column(name="entity_type", type="smallint")
     */
    private $entityType;
     
    /**
     * @ORM\OneToMany(targetEntity="Entity", mappedBy="entityCategory")
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
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;
    
        return $this;
    }

    /**
     * Get entityType
     *
     * @return array 
     */
    public function getEntityType()
    {
        return $this->entityType;
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
