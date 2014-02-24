<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Relation
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\RelationTypeRepository")
 * @ORM\Table(name="relation_type")
 */
class RelationType
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
     * @var integer
     *
     * @ORM\Column(name="inverse_type", type="integer", nullable=true)
     */
    private $inverseTypeId;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="RelationType")
     * @ORM\JoinColumn(name="inverse_type", referencedColumnName="id", onDelete="SET NULL", nullable=true)
     */
    private $inverseType;

    /**
     * @var boolean
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;
     
    /**
     * @ORM\OneToMany(targetEntity="Relation", mappedBy="relationType")
     */
    private $relations;
    
    public function __construct()
    {
        $this->relations = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    public function isPublic()
    {
        return is_null($this->userId);
    }

    public function getInverseTypeId()
    {
        return $this->inverseTypeId;
    }

    public function setInverseType($inverseType)
    {
        $this->inverseType = $inverseType;

        return $this;
    }

    public function getInverseType()
    {
        return $this->inverseType;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Request
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Add relations
     *
     * @param \CM\CMBundle\Relation\EventDate $eventDates
     * @return Event
     */
    public function addRelation(Relation $relation)
    {
        if (!$this->relations->contains($relation)) {
            $this->relations[] = $relation;
            $relation->setRelationType($this);
        }
    
        return $this;
    }

    /**
     * Remove relations
     *
     * @param \CM\CMBundle\Relation\EventDate $eventDates
     */
    public function removeRelation(Relation $relation)
    {
        $this->relations->removeElement($relation);
    }

    /**
     * Get relations
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRelations()
    {
        return $this->relations;
    }
}
