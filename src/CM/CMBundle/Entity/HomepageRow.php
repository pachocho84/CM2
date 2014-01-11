<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * HomepageRow
 *
 * @ORM\Entity(repositoryClass="HomepageRowRepository")
 * @ORM\Table(name="homepage_row")
 */
class HomepageRow
{
    use ORMBehaviors\Timestampable\Timestampable;

    const TYPE_ROW_1 = 0;
    const TYPE_ROW_2 = 1;
    const TYPE_ROW_3 = 2;
    const TYPE_ROW_4 = 3;
    const TYPE_PHOTO_GALLERY = 4;
    const TYPE_VIDEO_GALLERY = 5;
    const TYPE_REVIEWS = 6;
    const TYPE_BANNER_SUBSCRIBE = 7;
    const TYPE_PARTNER_ACCADEMIA_TEATRO_ALLA_SCALA = 8;
    const TYPE_PARTNER_RADIO_CLASSICA = 9;

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    /**
     * @var integer
     *
     * @ORM\Column(name="`order`", type="integer")
     */
    private $order;

    /**
     * @var boolean
     *
     * @ORM\Column(name="visible", type="boolean")
     */
    private $visible = true;
    
    /**
     * @ORM\OneToMany(targetEntity="HomepageColumn", mappedBy="row", cascade={"persist", "remove"})
     */
    private $columns;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->columns = new ArrayCollection();
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
     * Set type
     *
     * @param integer $type
     * @return HomepageRow
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    public function getTypeString()
    {
        switch ($this->type) {
            case self::TYPE_ROW_1:
                return 'row1';
            case self::TYPE_ROW_2:
                return 'row2';
            case self::TYPE_ROW_3:
                return 'row3';
            case self::TYPE_ROW_4:
                return 'row4';
            case self::TYPE_PHOTO_GALLERY:
                return 'photo_gallery';
            case self::TYPE_VIDEO_GALLERY:
                return 'video_gallery';
            case self::TYPE_REVIEWS:
                return 'reviews';
            case self::TYPE_BANNER_SUBSCRIBE:
                return 'banner_subscrive';
            case self::TYPE_PARTNER_ACCADEMIA_TEATRO_ALLA_SCALA:
                return 'partner_accademia_teatro_alla_scala';
            case self::TYPE_PARTNER_RADIO_CLASSICA:
                return 'partner_radio_classica';
        }
    }

    /**
     * Set `order`
     *
     * @param integer $`order`
     * @return HomepageRow
     */
    public function setOrder($order)
    {
        $this->order = $order;
    
        return $this;
    }

    /**
     * Get `order`
     *
     * @return integer 
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return HomepageRow
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
     * Add event_dates
     *
     * @param HomepageColumn $column
     * @return Event
     */
    public function addColumn(HomepageColumn $column)
    {
        if (!$this->columns->contains($column)) {
            $this->columns[] = $column;
            $column->setRow($this);
        }
    
        return $this;
    }

    /**
     * Remove event_dates
     *
     * @param HomepageColumn $column
     */
    public function removeColumn(HomepageColumn $column)
    {
        $this->columns->removeElement($column);
    }

    /**
     * Get event_dates
     *
     * @return Collection 
     */
    public function getColumns()
    {
        return $this->columns;
    }
}
