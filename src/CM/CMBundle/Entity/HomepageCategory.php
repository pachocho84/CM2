<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;

/**
 * HomepageCategory
 *
 * @ORM\Entity(repositoryClass="HomepageCategoryRepository")
 * @ORM\Table(name="homepage_category")
 */
class HomepageCategory
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
     * @ORM\Column(name="rubric", type="boolean")
     */
    private $rubric = false;

    /**
     * @ORM\Column(name="editor_id", type="integer")
     */
    private $editorId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="editor_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     **/
    private $editor;

    /**
     * @var string
     *
     * @ORM\Column(name="`update`", type="string", length=150, nullable=true)
     */
    private $update;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->translate('en');
        $this->mergeNewTranslations();
    }

    public function __call($method, $arguments)
    {
        return $this->proxyCurrentLocaleTranslation($method, $arguments);
    }

    public function __toString()
    {
        return $this->proxyCurrentLocaleTranslation('__toString');
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

    /**
     * Set rubric
     *
     * @param boolean $rubric
     * @return HomepageCategory
     */
    public function setRubric($rubric)
    {
        $this->rubric = $rubric;
    
        return $this;
    }

    /**
     * Get rubric
     *
     * @return boolean 
     */
    public function getRubric()
    {
        return $this->rubric;
    }

    /**
     * Get editorId
     *
     * @return integer 
     */
    public function getEditorId()
    {
        return $this->editorId;
    }

    /**
     * Set editor
     *
     * @param Editor $editor
     * @return Like
     */
    public function setEditor(User $editor)
    {
        $this->editor = $editor;
        if (!is_null($editor)) {
            $this->editorId = $editor->getId();
        }
    
        return $this;
    }

    /**
     * Get editor
     *
     * @return Editor 
     */
    public function getEditor()
    {
        return $this->editor;
    }

    /**
     * Set `update`
     *
     * @param string $`update`
     * @return HomepageCategory
     */
    public function setUpdate($update)
    {
        $this->update = $update;
    
        return $this;
    }

    /**
     * Get `update`
     *
     * @return string 
     */
    public function getUpdate()
    {
        return $this->update;
    }
}
