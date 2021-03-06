<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ImageAlbum
 *
 * @ORM\Entity(repositoryClass="ImageAlbumRepository")
 * @ORM\Table(name="image_album")
 */
class ImageAlbum extends Entity 
{
    const TYPE_PROFILE = 0;
    const TYPE_COVER = 1;
    const TYPE_BACKGROUND = 2;
    const TYPE_ALBUM = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * __toString function.
     * 
     * @access public
     * @return void
     */
    public function __toString()
    {
        switch ($this->type) {
            case self::TYPE_PROFILE:
                return 'profile picture';
                break;
            case self::TYPE_COVER:
                return 'cover picture';
                break;
            case self::TYPE_BACKGROUND:
                return 'background picture';
                break;
            case self::TYPE_ALBUM:
                return $this->getTitle();
                break;
        }
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
     * @return ImageAlbum
     */
    public function setType($type)
    {
        if (!in_array($type, array(self::TYPE_ALBUM, self::TYPE_PROFILE, self::TYPE_COVER, self::TYPE_BACKGROUND))) {
            throw new \InvalidArgumentException("Invalid status");
        }
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
}
