<?php

namespace CM\CMBundle\Service;

use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\EntityUser;

class NotificationCenter
{
    private $em;

    private $flushNeeded = false;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function flushNeeded()
    {
        return $this->flushNeeded;
    }

    public function flushed()
    {
        $this->flushNeeded = false;
    }

    public function newNotification(
        $type,
        $toUser,
        $fromUser,
        $object,
        $objectId,
        $post = null,
        $page = null,
        $group = null
    )
    {
        if (!is_null($toUser) && $toUser->getId() == $fromUser->getId()) {
            return;
        }

        $notification = new Notification;
        $notification->setType($type)
            ->setUser($toUser)
            ->setFromUser($fromUser);
        if (!is_null($post)) {
            $notification->setPost($post);
        }
        if (!is_null($object)) {
            $notification->setObject($object)
                ->setObjectId($objectId);
        }
        if (!is_null($page)) {
            $notification->setFromPage($page);
        } elseif (!is_null($group)) {
            $notification->setFromGroup($group);
        }
        $this->em->persist($notification);
        $this->flushNeeded = true;
    }

public function getNewNotificationsNumber($userId)
    {
        return $this->em->getRepository('CMBundle:Notification')->getNumberNew($userId);
    }

    public function seeNotifications($userId)
    {
        $this->em->getRepository('CMBundle:Notification')->updateStatus($userId, null, null, Notification::STATUS_NEW);

        return $this->getNewNotificationsNumber($userId);
    }

    public function removeNotifications($fromUserId, $object, $objectId, $type)
    {
        $this->em->getRepository('CMBundle:Notification')->delete($fromUserId, $object, $objectId, $type);

        $this->flushNeeded = true;
    }
}