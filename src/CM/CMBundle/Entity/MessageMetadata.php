<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\MessageMetadata as BaseMessageMetadata;
use FOS\MessageBundle\Model\MessageInterface;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="message_metadata")
 */
class MessageMetadata extends BaseMessageMetadata
{
    const STATUS_NEW = 0;
    const STATUS_UNREAD = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_DELETED = 3;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="message_id", type="integer", nullable=false)
     */
    private $messageId;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="metadata")
     * @ORM\JoinColumn(name="message_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var MessageInterface
     */
    protected $message;

    /**
     * @ORM\Column(name="participant_id", type="integer", nullable=false)
     */
    private $participantId;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="participant_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var ParticipantInterface
     */
    protected $participant;

    /**
     * @ORM\Column(name="status", type="smallint")
     */
    protected $status = self::STATUS_NEW;

    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param  MessageInterface $message
     * @return null
     */
    public function setMessage(MessageInterface $message)
    {
        $this->message = $message;
        if (!is_null($message)) {
            $this->messageId = $message->getId();
        }
    
        return $this;
    }

    public function getParticipantId()
    {
        return $this->participantId;
    }

    public function setParticipant(ParticipantInterface $participant)
    {
        $this->participant = $participant;
        if (!is_null($participant)) {
            $this->participantId = $participant->getId();
        }
    
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
        $this->isRead = $this->status != self::STATUS_UNREAD;

        return $this;
    }

    /**
     * @param boolean $isRead
     * @return null
     */
    public function setIsRead($isRead)
    {
        $this->isRead = (boolean)$isRead;
        if ($this->status == self::STATUS_UNREAD && $this->isRead) {
            $this->status = self::STATUS_ACTIVE;
        } elseif ($this->status == self::STATUS_ACTIVE && !$this->isRead) {
            $this->status = self::STATUS_UNREAD;
        }
    }

}