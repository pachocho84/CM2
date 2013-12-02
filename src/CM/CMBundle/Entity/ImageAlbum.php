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
    const TYPE_ALBUM = 0;
    const TYPE_PROFILE = 1;
    const TYPE_COVER = 2;
    const TYPE_BACKGROUND = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="type", type="smallint", nullable=false)
     */
    private $type;

    public function __construct()
    {
        parent::__construct();

        $this->setVisible(true);
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
