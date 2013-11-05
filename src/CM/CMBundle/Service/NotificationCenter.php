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

    public function flush()
    {
        if ($this->flushNeeded) {
            $this->flushNeeded = false;
            $this->em->flush();
        }
    }

    public function newNotification(
        $type,
        $toUser,
        $fromUser,
        $post = null,
        $image = null,
        $page = null,
        $group = null
    )
    {
        if ($toUser->getId() == $fromUser->getId()) {
            return;
        }
        $notification = new Notification;
        $notification->setType($type)
            ->setUser($toUser)
            ->setFromUser($fromUser);
        if (!is_null($post)) {
            $notification->setPost($post);
        }
        if (!is_null($image)) {
            $notification->setImage($image);
        }
        if (!is_null($page)) {
            $notification->setFromPage($page);
        } elseif (!is_null($group)) {
            $notification->setFromGroup($group);
        }
        $this->em->persist($notification);
        $this->flushNeeded = true;
    }

    public function removeNotifications($toUser, $object, $objectId)
    {
        $notifications = $this->em->getRepository('CMBundle:Notification')->getFor($toUser, $object, $objectId);
        foreach ($notifications as $notification) {
            $this->em->remove($notification);
            $this->flushNeeded = true;
        }
    }
}