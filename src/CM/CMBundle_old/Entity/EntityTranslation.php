<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * @ORM\Entity
 * @ORM\Table(name="entity_translation",
 *   uniqueConstraints={@ORM\UniqueConstraint(name="lookup_unique_idx", columns={
 *     "locale", "object_id", "field"
 *   })}
 * )
 */
class EntityTranslation extends AbstractPersonalTranslation
{
    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct($locale = null, $field = null, $content = null)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($content);
    }
    
    /**
     * @ORM\ManyToOne(targetEntity="CM\CMBundle\Entity\Entity", inversedBy="translations")
     * @ORM\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")	
     */
    protected $object;
}