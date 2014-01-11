<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\MessageBundle\Entity\ThreadMetadata as BaseThreadMetadata;
use FOS\MessageBundle\Model\ThreadInterface;
use FOS\MessageBundle\Model\ParticipantInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="message_thread_metadata")
 */
class MessageThreadMetadata extends BaseThreadMetadata
{
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
     * @ORM\ManyToOne(targetEntity="MessageThread", inversedBy="metadata")
     * @ORM\JoinColumn(name="thread_id", referencedColumnName="id", onDelete="CASCADE", nullable=false)
     * @var ThreadInterface
     */
    protected $thread;

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

    public static function className()
    {
        return get_class();
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
}