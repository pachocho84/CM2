<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * Image
 *
 * @ORM\Table(name="image")
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\ImageRepository")
 */
class Image
{
    use ORMBehaviors\Timestampable\Timestampable;
        
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
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="images")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)	
     */
    private $entity;
    
    /**
     * @var string
     *
     * @ORM\Column(name="img", type="string", length=100)
     * @Assert\Image(minWidth = 250, maxWidth = 750, minHeight = 250, maxHeight = 750, maxSize = "8M")
     */
    private $img;

    /**
     * @var integer
     *
     * @ORM\Column(name="offset", type="smallint", nullable=true)
     */
    private $offset;

    /**
     * @var boolean
     *
     * @ORM\Column(name="main", type="boolean", nullable=true)
     */
    private $main;

    /**
     * @var integer
     *
     * @ORM\Column(name="sequence", type="smallint", nullable=true)
     */
    private $sequence;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

	public function __toString()
	{
		return $this->img;
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
     * Set entityId
     *
     * @param integer $entityId
     * @return Image
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
    
        return $this;
    }

    /**
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set img
     *
     * @param string $img
     * @return Image
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
     * Set offset
     *
     * @param integer $offset
     * @return Image
     */
    public function setOffset($offset)
    {
        $this->offset = $offset;
    
        return $this;
    }

    /**
     * Get offset
     *
     * @return integer 
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * Set main
     *
     * @param boolean $main
     * @return Image
     */
    public function setMain($main)
    {
        $this->main = $main;
    
        return $this;
    }

    /**
     * Get main
     *
     * @return boolean 
     */
    public function getMain()
    {
        return $this->main;
    }

    /**
     * Set sequence
     *
     * @param integer $sequence
     * @return Image
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
    
        return $this;
    }

    /**
     * Get sequence
     *
     * @return integer 
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return Image
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
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setEntity(Event $entity = null)
    {
        $this->entity = $entity;
    
        return $this;
    }

    /**
     * Get entity
     *
     * @return Entity 
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
