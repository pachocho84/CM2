<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * DiscTrack
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\DiscTrackRepository")
 * @ORM\Table(name="disc_track")
 * @ORM\HasLifecycleCallbacks
 */
class DiscTrack
{
    use \CM\CMBundle\Model\AudioTrait;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="disc_id", type="integer")
     **/
    private $discId;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Disc", inversedBy="discTracks")
     * @ORM\JoinColumn(name="disc_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $disc;

    /**
     * @var integer
     *
     * @ORM\Column(name="number", type="smallint")
     */
    private $number;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=150, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="composer", type="string", length=150)
     */
    private $composer;

    /**
     * @var string
     *
     * @ORM\Column(name="movement", type="string", length=150, nullable=true)
     */
    private $movement;

    /**
     * @var array
     *
     * @ORM\Column(name="artists", type="string", length=150)
     */
    private $artists;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="duration", type="time")
     */
    private $duration;

    public function __toString()
    {
        return $this->title;
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

    protected function getRootDir()
    {
        return __DIR__.'/../Resources/public/';
    }

    /**
     * Get discId
     *
     * @return integer 
     */
    public function getDiscId()
    {
        return $this->discId;
    }

    /**
     * Set disc
     *
     * @param Disc $disc
     * @return DiscDate
     */
    public function setDisc(Disc $disc = null)
    {
        $this->disc = $disc;
        $this->discId = $disc->getId();
    
        return $this;
    }

    /**
     * Get disc
     *
     * @return Disc 
     */
    public function getDisc()
    {
        return $this->disc;
    }

    /**
     * Set number
     *
     * @param integer $number
     * @return DiscTrack
     */
    public function setNumber($number)
    {
        $this->number = $number;
    
        return $this;
    }

    /**
     * Get number
     *
     * @return integer 
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set composer
     *
     * @param string $composer
     * @return DiscTrack
     */
    public function setComposer($composer)
    {
        $this->composer = $composer;
    
        return $this;
    }

    /**
     * Get composer
     *
     * @return string 
     */
    public function getComposer()
    {
        return $this->composer;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return DiscTrack
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
     * Set movement
     *
     * @param string $movement
     * @return DiscTrack
     */
    public function setMovement($movement)
    {
        $this->movement = $movement;
    
        return $this;
    }

    /**
     * Get movement
     *
     * @return string 
     */
    public function getMovement()
    {
        return $this->movement;
    }

    /**
     * Set artists
     *
     * @param array $artists
     * @return Disc
     */
    public function setArtists($artists)
    {
        $this->artists = $artists;
    
        return $this;
    }

    /**
     * Get artists
     *
     * @return array 
     */
    public function getArtists()
    {
        return $this->artists;
    }

    /**
     * Set duration
     *
     * @param \DateTime $duration
     * @return DiscTrack
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    
        return $this;
    }

    /**
     * Get duration
     *
     * @return \DateTime 
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
