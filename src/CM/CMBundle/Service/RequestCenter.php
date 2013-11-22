<?php

namespace CM\CMBundle\Service;

use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\GroupUser;

class RequestCenter
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

    public function newRequest(
        $toUser,
        $fromUser,
        $object,
        $objectId,
        $entity = null,
        $page = null,
        $group = null
    )
    {
        if ($toUser->getId() == $fromUser->getId()) {
            return;
        }
        $request = new Request;
        $request->setUser($toUser)
            ->setFromUser($fromUser);
        if (!is_null($entity)) {
            $request->setEntity($entity);
        } else {
            $request->setObject($object)
                ->setObjectId($objectId);
        }
        if (!is_null($page)) {
            $request->setFromPage($page);
        } elseif (!is_null($group)) {
            $request->setGroup($group);
        }
        $this->em->persist($request);
        $this->flushNeeded = true;
    }

    public function getNewRequestsNumber($userId)
    {
        return $this->em->getRepository('CMBundle:Request')->getNumberNew($userId);
    }

    public function seeRequests($userId)
    {
        $this->em->getRepository('CMBundle:Request')->updateStatus($userId, array(), Request::STATUS_NEW);

        return $this->getNewRequestsNumber($userId);
    }

    public function acceptRequest($userId, $options = array())
    {
        $request = $this->em->getRepository('CMBundle:Request')->updateStatus($userId, $options, null, Request::STATUS_ACCEPTED);

        if (!is_null($request->getEntity())) {
            $entityUser = $this->em->getRepository('CMBundle:EntityUser')->findOneBy(array('userId' => $userId, 'entityId' => $request->getEntityId()));
            $entityUser->setStatus(EntityUser::STATUS_ACTIVE);
            $this->em->persist($entityUser);

            $this->flushNeeded = true;
        } elseif (!is_null($request->getGroup())) {
            $groupUser = $this->em->getRepository('CMBundle:GroupUser')->findOneBy(array('userId' => $userId, 'groupId' => $request->getGroupId()));
            $groupUser->setStatus(GroupUser::STATUS_ACTIVE);
            $this->em->persist($groupUser);

            $this->flushNeeded = true;
        } else {
            switch ($request->getObject()) {
                case 'CM\CMBundle\Entity\Event':

                    break;
                case 'CM\CMBundle\Entity\Group':

                    break;
            }
        }
    }

    public function refuseRequest($userId, $options = array())
    {
        $request = $this->em->getRepository('CMBundle:Request')->updateStatus($userId, $options, null, Request::STATUS_REFUSED);
        
        if (!is_null($request->getEntity())) {
            $entityUser = $this->em->getRepository('CMBundle:EntityUser')->findOneBy(array('userId' => $userId, 'entityId' => $options['entityId']));
            $newEntityUserStatus = $entityUser->getStatus() == EntityUser::STATUS_PENDING ? EntityUser::STATUS_REFUSED_ENTITY_USER : EntityUser::STATUS_REFUSED_ADMIN;
            $entityUser->setStatus($newEntityUserStatus);
            $this->em->persist($entityUser);

            $this->em->createQueryBuilder('r')
                ->delete('CMBundle:Request', 'r')
                ->where('r.fromUser = :user_id')->setParameter('user_id', $userId)
                ->andWhere('r.entityId = :entity_id')->setParameter('entity_id', $options['entityId'])
                ->getQuery()
                ->execute();

            $this->flushNeeded = true;
        } elseif (!is_null($request->getGroup())) {
            $groupUser = $this->em->getRepository('CMBundle:GroupUser')->findOneBy(array('userId' => $userId, 'groupId' => $request->getObjectId()));
            $newGroupUserStatus = $groupUser->getStatus() == GroupUser::STATUS_PENDING ? GroupUser::STATUS_REFUSED_GROUP_USER : GroupUser::STATUS_REFUSED_ADMIN;
            $groupUser->setStatus($newGroupUserStatus);
            $this->em->persist($groupUser);
            
            $this->em->createQueryBuilder('r')
                ->delete('CMBundle:Request', 'r')
                ->where('r.fromUser = :user_id')->setParameter('user_id', $userId)
                ->andWhere('r.groupId = :group_id')->setParameter('group_id', $options['groupId'])
                ->getQuery()
                ->execute();

            $this->flushNeeded = true;
        } else {
            switch ($request->getObject()) {
                case 'CM\CMBundle\Entity\Event':

                    break;
                case 'CM\CMBundle\Entity\Group':

                    break;
            }
        }
    }

    public function removeRequest($user, $options = array())
    {
        $this->em->getRepository('CMBundle:Request')->delete($user, $options, true);
    }
}