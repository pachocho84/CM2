<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="entity_translation",
 *     uniqueConstraints={@ORM\UniqueConstraint(columns={
 *         "translatable_id", "locale"
 *     })}
 * )
 */
class EntityTranslation
{
    use ORMBehaviors\Sluggable\Sluggable;
    use ORMBehaviors\Translatable\Translation;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150, nullable=true)
     * @Assert\NotBlank(groups="Event")
     * @Assert\Blank(groups="Biography")
     * @Assert\Length(
     *      min = "2",
     *      max = "150",
     *      minMessage = "The title must be at least {{ limit }} characters length",
     *      maxMessage = "The title cannot be longer than {{ limit }} characters length"
     * )
     * @Assert\Regex(
     *     pattern="/\b[A-Z]{3,}(?!M{0,2}(CM|CD|D?C{0,1})(XC|XL|L?X{0,1})(IX|IV|V?I{0,1})\b)/",
     *     match=false,
     *     message="Please remove all UPPERCASE words (roman numbers allowed)"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="subtitle", type="string", length=250, nullable=true)
     * @Assert\Length(
     *      max = "250",
     *      maxMessage = "The subtitle cannot be longer than {{ limit }} characters length"
     * )
     * @Assert\Regex(
     *     pattern="/\b[A-Z]{3,}(?!M{0,2}(CM|CD|D?C{0,1})(XC|XL|L?X{0,1})(IX|IV|V?I{0,1})\b)/",
     *     match=false,
     *     message="Please remove all UPPERCASE words (roman numbers allowed)"
     * )
     */
    private $subtitle;

    /**
     * @var string
     *
     * @ORM\Column(name="extract", type="text", nullable=true)
     * @Assert\Length(
     *      max = "500",
     *      maxMessage = "The extract cannot be longer than {{ limit }} characters length"
     * )
     * @Assert\Regex(
     *     pattern="/\b[A-Z]{3,}(?!M{0,2}(CM|CD|D?C{0,1})(XC|XL|L?X{0,1})(IX|IV|V?I{0,1})\b)/",
     *     match=false,
     *     message="Please remove all UPPERCASE words (roman numbers allowed)"
     * )
     */
    private $extract;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     * @Assert\Length(
     *      groups="Event",
     *      min = "12",
     *      minMessage = "The title must be at least {{ limit }} characters length"
     * )
     * @Assert\Regex(
     *     pattern="/\b[A-Z]{3,}(?!M{0,2}(CM|CD|D?C{0,1})(XC|XL|L?X{0,1})(IX|IV|V?I{0,1})\b)/",
     *     match=false,
     *     message="Please remove all UPPERCASE words (roman numbers allowed)"
     * )
     */
    private $text;

    public function __construct()
    {
        // $this->locale = 'en';
    }
    
    public function __toString()
    {
        return $this->getTitle();
    }

    public static function className()
    {
        return get_class();
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getEntity()
    {
        return $this->translatable;
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
     * Get sluggable fields
     * 
     * @access public
     * @return void
     */
    public function getSluggableFields()
    {
        return ['title'];
    }
}