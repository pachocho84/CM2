<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="CM\CMBundle\Entity\EventRepository")
 */
class Event extends Entity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;
    
    /**
     * @ORM\OneToMany(targetEntity="EventDate", mappedBy="event", cascade={"persist", "remove"})
	 */
	private $event_dates;
	
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
/*     	parent::__construct(); */
    
        $this->event_dates = new ArrayCollection();
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
     * @param \CM\CMBundle\Entity\EventDate $eventDates
     * @return Event
     */
    public function addEventDate(EventDate $eventDates)
    {
        if (!$this->event_dates->contains($eventDates)) {
            $this->event_dates[] = $eventDates;
            $eventDates->setEvent($this);
        }
    
        return $this;
    }

    /**
     * Remove event_dates
     *
     * @param \CM\CMBundle\Entity\EventDate $eventDates
     */
    public function removeEventDate(EventDate $eventDates)
    {
        $this->event_dates->removeElement($eventDates);
    }

    /**
     * Get event_dates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventDates()
    {
        return $this->event_dates;
    }
}