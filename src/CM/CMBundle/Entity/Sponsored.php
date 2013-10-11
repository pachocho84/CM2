<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use CM\UserBundle\Entity\User;

/**
 * Sponsored
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Sponsored
{
    use ORMBehaviors\Timestampable\Timestampable;
    
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
     * @ORM\ManyToOne(targetEntity="Event")
     * @ORM\JoinColumn(name="entity_id", referencedColumnName="id", onDelete="CASCADE", nullable=true)    
     */
    private $event;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="CM\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)    
     */
    private $user;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date_from", type="date")
     */
    private $dateFrom;

    /**
     * @var \Date
     *
     * @ORM\Column(name="date_to", type="date")
     */
    private $dateTo;

    /**
     * @var integer
     *
     * @ORM\Column(name="views", type="integer")
     */
    private $views;


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
     * Set dateFrom
     *
     * @param \Date $dateFrom
     * @return Sponsored
     */
    public function setDateFrom($dateFrom)
    {
        $this->dateFrom = $dateFrom;
    
        return $this;
    }

    /**
     * Get dateFrom
     *
     * @return \Date 
     */
    public function getDateFrom()
    {
        return $this->dateFrom;
    }

    /**
     * Set dateTo
     *
     * @param \Date $dateTo
     * @return Sponsored
     */
    public function setDateTo($dateTo)
    {
        $this->dateTo = $dateTo;
    
        return $this;
    }

    /**
     * Get dateTo
     *
     * @return \Date 
     */
    public function getDateTo()
    {
        return $this->dateTo;
    }

    /**
     * Set views
     *
     * @param integer $views
     * @return Sponsored
     */
    public function setViews($views)
    {
        $this->views = $views;
    
        return $this;
    }

    /**
     * Get views
     *
     * @return integer 
     */
    public function getViews()
    {
        return $this->views;
    }

    /**
     * Set event
     *
     * @param \CM\CMBundle\Entity\Event $event
     * @return Sponsored
     */
    public function setEvent(\CM\CMBundle\Entity\Event $event = null)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * Get event
     *
     * @return \CM\CMBundle\Entity\Event 
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param \CM\UserBundle\Entity\User $user
     * @return Sponsored
     */
    public function setUser(\CM\UserBundle\Entity\User $user)
    {
        $this->user = $user;
    
        return $this;
    }

    /**
     * Get user
     *
     * @return \CM\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}