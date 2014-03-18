<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Entity\Thread as BaseThread;

/**
 * @ORM\Entity(repositoryClass="MessageThreadRepository")
 * @ORM\Table(name="message_thread")
 * @ORM\HasLifecycleCallbacks
 */
class MessageThread extends BaseThread
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="creator_id", type="integer")
     */
    private $creatorId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="creator_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     */
    protected $createdBy;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="thread")
     * @var Message[]|\Doctrine\Common\Collections\Collection
     */
    protected $messages;

    /**
     * @ORM\OneToMany(targetEntity="MessageThreadMetadata", mappedBy="thread", cascade={"all"})
     * @var ThreadMetadata[]|\Doctrine\Common\Collections\Collection
     */
    protected $metadata;

    /**
     * @var \DateTime $updatedAt
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updatedAt;

    public static function className()
    {
        return get_class();
    }

    /**
     * Get creatorId
     *
     * @return integer 
     */
    public function getCreatorId()
    {
        return $this->creatorId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setCreator(User $creator = null)
    {
        $this->createdBy = $creator;
        if (!is_null($creator)) {
            $this->creatorId = $creator->getId();
        }
    
        return $this;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getCreator()
    {
        return $this->createdBy;
    }

    /**
     * Get user
     *
     * @return User 
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Returns updatedAt value.
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Updates createdAt and updatedAt timestamps.
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamps()
    {
        $this->updatedAt = new \DateTime('now');
    }
}