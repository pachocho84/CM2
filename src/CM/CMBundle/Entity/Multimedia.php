<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Multimedia
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\MultimediaRepository")
 * @ORM\Table(name="multimedia")
 */
class Multimedia extends Entity
{
    const TYPE_YOUTUBE = 0;
    const TYPE_VIMEO = 1;
    const TYPE_SOUNDCLOUD = 2;

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

    /**
     * @var integer
     *
     * @ORM\Column(name="link", type="string", length=150, nullable=false)
     */
    private $link;

    public function __construct()
    {
        parent::__construct();
        
        $this->translate('en');
        $this->mergeNewTranslations();
    }

    public function typeString()
    {
        switch ($this->type) {
            case self::TYPE_YOUTUBE:
                return 'youtube video';
            case self::TYPE_VIMEO:
                return 'vimeo video';
            case self::TYPE_SOUNDCLOUD:
                return 'soundcloud track';
        }
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
     * @return Multimedia
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

    /**
     * Set link
     *
     * @param integer $link
     * @return Multimedia
     */
    public function setLink($link)
    {
        $this->link = $link;
    
        return $this;
    }

    /**
     * Get link
     *
     * @return integer 
     */
    public function getLink()
    {
        return $this->link;
    }

    public static function youtubePattern()
    {
        return '~(?:http|https|)(?::\/\/|)(?:www.|)(?:youtu\.be\/|youtube\.com(?:\/embed\/|\/v\/|\/watch\?v=|\/ytscreeningroom\?v=|\/feeds\/api\/videos\/|\/user\S*[^\w\-\s]|\S*[^\w\-\s]))([\w\-]{11})[a-z0-9;:@?&%=+\/\$_.-]*~i';
    }
}
