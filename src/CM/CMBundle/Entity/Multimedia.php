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
     * @ORM\Column(name="entity_id", type="integer", nullable=true)
     **/
    private $entityId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Entity", inversedBy="multimedia")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)
     */
    private $entity;

    public function __construct()
    {
        parent::__construct();
        
        $this->translate('en');
        $this->mergeNewTranslations();
    }

    public static function className()
    {
        return get_class();
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
     * Get entityId
     *
     * @return integer 
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set entity
     *
     * @param Entity $entity
     * @return Image
     */
    public function setEntity(Entity $entity = null)
    {
        $this->entity = $entity;
        if (!is_null($entity)) {
            $this->entityId = $entity->getId();
        }
    
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

    public function setUrl($url, $scheme = 'https')
    {
        switch (substr(preg_split('/(www|m)\./', parse_url($url, PHP_URL_HOST), null, PREG_SPLIT_NO_EMPTY)[0], 0, 4)) {
            case 'yout':
                $info = json_decode(file_get_contents($scheme.'://www.youtube.com/oembed?format=json&url='.urlencode($url)));
                $this->setType(Multimedia::TYPE_YOUTUBE)
                    ->setSource(preg_replace('/^.*embed\/(.*)\?.*/', '$1', $info->html));
                $info = json_decode(file_get_contents($scheme.'://gdata.youtube.com/feeds/api/videos/'.$url.'?v=2&alt=jsonc'))->data;
                break;
            case 'vime':
                $info = json_decode(file_get_contents($scheme.'://vimeo.com/api/oembed.json?url='.urlencode($url)));
                $this->setType(Multimedia::TYPE_VIMEO)
                    ->setSource($info->video_id);
                break;
            case 'soun':
                $info = json_decode(file_get_contents($scheme.'://soundcloud.com/oembed.json?url='.urlencode($url)));
                $this->setType(Multimedia::TYPE_SOUNDCLOUD)
                    ->setSource(preg_replace('/^.*tracks%2F(.*)&.*/', '$1', $info->html));
                break;
        }

        $this->setTitle($info->title)
            ->setText($info->description);
    }
}
