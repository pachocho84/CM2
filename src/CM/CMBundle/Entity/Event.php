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
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\OneToMany(targetEntity="EventDate", mappedBy="event", cascade={"persist", "remove"})
		 */
	 	private $eventDates;
    
    /**
     * @ORM\OneToMany(targetEntity="Post", mappedBy="entity", cascade={"persist", "remove"})
		 */
	 	private $posts;
	
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
    public function __construct(EventDate $eventDate = null)
    {
    	parent::__construct();

    	$this->posts = new ArrayCollection();
    	
    	$this->eventDates = new ArrayCollection();
    	if (! is_null($eventDate)) {
    		$this->addEventDate($eventDate);
    	}
    }

    public function __toString()
    {
    	return "Event";
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
     * @param \CM\CMBundle\Entity\EventDate $eventDate
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
     * @param \CM\CMBundle\Entity\EventDate $eventDate
     */
    public function removeEventDate(EventDate $eventDate)
    {
        $this->eventDates->removeElement($eventDate);
    }

    /**
     * Get event_dates
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEventDates()
    {
        return $this->eventDates;
    }

    /**
     * Add posts
     *
     * @param \CM\CMBundle\Entity\Post $posts
     * @return Event
     */
    public function addPost(\CM\CMBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;
        $post->setEvent($this);
    
        return $this;
    }

    /**
     * Remove posts
     *
     * @param \CM\CMBundle\Entity\Post $posts
     */
    public function removePost(\CM\CMBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPosts()
    {
        return $this->posts;
    }
}