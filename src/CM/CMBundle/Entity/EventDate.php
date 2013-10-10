<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventDate
 *
 * @ORM\Table(name="event_date")
 * @ORM\Entity
 */
class EventDate
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="eventDates")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $event;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetimetz")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetimetz", nullable=true)
     */
    private $end;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=150)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = "2",
     *      max = "150",
     *      minMessage = "The location must be at least {{ limit }} characters length",
     *      maxMessage = "The location cannot be longer than {{ limit }} characters length"
     * )
     */
    private $location;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="string", length=150)
     * @Assert\NotBlank
     * @Assert\Length(
     *      min = "2",
     *      max = "150",
     *      minMessage = "The location must be at least {{ limit }} characters length",
     *      maxMessage = "The location cannot be longer than {{ limit }} characters length"
     * )
     */
    private $address;

    /**
     * @var string
     *
     * @ORM\Column(name="coordinates", type="string", length=150)
     */
    private $coordinates;

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
     * Set eventId
     *
     * @param integer $eventId
     * @return EventDate
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;
    
        return $this;
    }

    /**
     * Get eventId
     *
     * @return integer 
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set start
     *
     * @param \DateTime $start
     * @return EventDate
     */
    public function setStart($start)
    {
        $this->start = $start;
    
        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime 
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     * @return EventDate
     */
    public function setEnd($end)
    {
        $this->end = $end;
    
        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime 
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return EventDate
     */
    public function setLocation($location)
    {
        $this->location = $location;
    
        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set address
     *
     * @param string $address
     * @return EventDate
     */
    public function setAddress($address)
    {
        $this->address = $address;
    
        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set coordinates
     *
     * @param string $coordinates
     * @return EventDate
     */
    public function setCoordinates($coordinates)
    {
        $this->coordinates = $coordinates;
    
        return $this;
    }

    /**
     * Get coordinates
     *
     * @return string 
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }

    /**
     * Set event
     *
     * @param Event $event
     * @return EventDate
     */
    public function setEvent(Event $event = null)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return Event 
     */
    public function getEvent()
    {
        return $this->event;
    }
}