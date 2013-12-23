<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Disc
 *
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\DiscRepository")
 * @ORM\Table(name="disc")
 */
class Disc extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var array
     *
     * @ORM\Column(name="authors", type="string", length=150)
     */
    private $authors;

    /**
     * @var array
     *
     * @ORM\Column(name="interpreters", type="string", length=150)
     */
    private $interpreters;

    /**
     * @var string
     *
     * @ORM\Column(name="`label`", type="string", length=150)
     */
    private $label;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="year", type="date")
     */
    private $year;
    
    /**
     * @ORM\OneToMany(targetEntity="DiscTrack", mappedBy="disc", cascade={"persist", "remove"})
     * @Assert\Valid
     */
    private $discTracks;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->translate('en');
        $this->mergeNewTranslations();
        
        $this->discTracks = new ArrayCollection();
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
     * Set authors
     *
     * @param array $authors
     * @return Disc
     */
    public function setAuthors($authors)
    {
        $this->authors = $authors;
    
        return $this;
    }

    /**
     * Get authors
     *
     * @return array 
     */
    public function getAuthors()
    {
        return $this->authors;
    }

    /**
     * Set interpreters
     *
     * @param array $interpreters
     * @return Disc
     */
    public function setInterpreters($interpreters)
    {
        $this->interpreters = $interpreters;
    
        return $this;
    }

    /**
     * Get interpreters
     *
     * @return array 
     */
    public function getInterpreters()
    {
        return $this->interpreters;
    }

    /**
     * Set label
     *
     * @param string $label
     * @return Disc
     */
    public function setLabel($label)
    {
        $this->label = $label;
    
        return $this;
    }

    /**
     * Get label
     *
     * @return string 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set year
     *
     * @param \DateTime $year
     * @return Disc
     */
    public function setYear($year)
    {
        $this->year = $year;
    
        return $this;
    }

    /**
     * Get year
     *
     * @return \DateTime 
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Add event_dates
     *
     * @param DiscTrack $discTrack
     * @return Event
     */
    public function addDiscTrack(DiscTrack $discTrack)
    {
        if (!$this->discTracks->contains($discTrack)) {
            $this->discTracks[] = $discTrack;
            $discTrack->setDisc($this);
        }
    
        return $this;
    }

    /**
     * Remove event_dates
     *
     * @param DiscTrack $discTrack
     */
    public function removeDiscTrack(DiscTrack $discTrack)
    {
        $this->discTracks->removeElement($discTrack);
    }

    /**
     * Get event_dates
     *
     * @return Collection 
     */
    public function getDiscTracks()
    {
        return $this->discTracks;
    }
}
