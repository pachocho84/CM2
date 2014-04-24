<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * EventDate
 *
 * @ORM\Entity
 * @ORM\Table(name="event_date")
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
     * @ORM\Column(name="event_id", type="integer")
     **/
    private $eventId;

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
     * @ORM\Column(name="start", type="datetimetz", nullable=false)
     * @Assert\DateTime
     * @Assert\NotNull
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetimetz", nullable=true)
     * @Assert\DateTime
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
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var string
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

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
     * Get eventId
     *
     * @return integer 
     */
    public function getEventId()
    {
        return $this->eventId;
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
        $this->eventId = $event->getId();
    
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
     * Set latitude
     *
     * @param string $latitude
     * @return EventDate
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return string 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param string $longitude
     * @return EventDate
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return string 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }
}