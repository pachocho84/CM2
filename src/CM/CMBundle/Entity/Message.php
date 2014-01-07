<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\Message as BaseMessage;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\MessageBundle\Model\MessageInterface;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use FOS\MessageBundle\Model\MessageMetadata;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 */
class Message extends BaseMessage implements MessageInterface
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="thread_id", type="integer", nullable=false)
     */
    private $threadId;

    /**
     * @ORM\ManyToOne(targetEntity="MessageThread", inversedBy="messages")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var ThreadInterface
     */
    protected $thread;

    /**
     * @ORM\Column(name="sender_id", type="integer", nullable=false)
     */
    private $senderId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="sender_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var ParticipantInterface
     */
    protected $sender;

    /**
     * @ORM\OneToMany(targetEntity="MessageMetadata", mappedBy="message", cascade={"all"})
     * @var MessageMetadata
     */
    protected $metadata;
    
    public function __toString()
    {
        return $this->body;
    }

    public function getThreadId()
    {
        return $this->threadId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setThread(ThreadInterface $thread)
    {
        $this->thread = $thread;
        if (!is_null($thread)) {
            $this->threadId = $thread->getId();
        }
    
        return $this;
    }

    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Like
     */
    public function setSender(ParticipantInterface $sender)
    {
        $this->sender = $sender;
        if (!is_null($sender)) {
            $this->senderId = $sender->getId();
        }
    
        return $this;
    }

    /**
     * @see FOS\MessageBundle\Model\MessageInterface::setBody()
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
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
}