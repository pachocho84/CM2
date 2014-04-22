<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * TagTranslation
 *
 * @ORM\Entity
 * @ORM\Table(name="tag_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "translatable_id", "locale"
 *     })}
 * )
 */
class TagTranslation
{
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;
    
    public function __toString()
    {
        return $this->getName();
    }

    public static function className()
    {
        return get_class();
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserTagTranslation
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
