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
        if ($this->flushNeeded) {
            $this->flushNeeded = false;
            return true;
        }

        return false;
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
        if ($toUser->getId() == $fromUser->getId()) {
            return;
        }
        // var_dump(array_keys(func_get_args()));
        // throw new \Exception("Error Processing Request", 1);

        $notification = new Notification;
        $notification->setType($type)
            ->setUser($toUser)
            ->setFromUser($fromUser);
        if (!is_null($post)) {
            $notification->setPost($post);
        } else {
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

    public function removeNotifications($toUser, $object, $objectId)
    {
        $notifications = $this->em->getRepository('CMBundle:Notification')->getFor($toUser, $object, $objectId);
        foreach ($notifications as $notification) {
            $this->em->remove($notification);
            $this->flushNeeded = true;
        }
    }
}