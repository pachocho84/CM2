<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Event
 *
 * @ORM\Entity(repositoryClass="EventRepository")
 * @ORM\Table(name="event")
 */
class Event extends Entity
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
     * @ORM\OneToMany(targetEntity="EventDate", mappedBy="event", cascade={"persist", "remove"}, orphanRemoval=true)
     * @Assert\Valid
     * @Assert\Count(min=1, minMessage="You must insert at least one date")
     */
    private $eventDates;
    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
/*         return $this->id; */
        return parent::getId();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->translate('en');
        $this->mergeNewTranslations();
        
        $this->eventDates = new ArrayCollection();
    }

    public static function className()
    {
        return get_class();
    }
    
    /**
     * Set id
     *
     * @param integer $id
     * @return Event
     */
    public function setId($id)
    {
        $this->id = $id;
    
        return $this;
    }

    /**
     * Add event_dates
     *
     * @param EventDate $eventDate
     * @return Event
     */
    public function addEventDate(EventDate $eventDate)
    {
        if (!$this->eventDates->contains($eventDate)) {
            $this->eventDates[] = $eventDate;
            $eventDate->setEvent($this);
        }
    
        return $this;
    }

    /**
     * Remove event_dates
     *
     * @param EventDate $eventDate
     */
    public function removeEventDate(EventDate $eventDate)
    {
        $this->eventDates->removeElement($eventDate);
    }

    /**
     * Get event_dates
     *
     * @return Collection 
     */
    public function getEventDates()
    {
        return $this->eventDates;
    }
}