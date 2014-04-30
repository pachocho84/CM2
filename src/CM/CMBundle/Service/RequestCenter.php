<?php

namespace CM\CMBundle\Service;

use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\PageUser;

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
        $page = null
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
        }
        if (!is_null($object)) {
            $request->setObject($object)
                ->setObjectId($objectId);
        }
        if (!is_null($page)) {
            $request->setPage($page);
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
        } elseif (!is_null($request->getPage())) {
            $pageUser = $this->em->getRepository('CMBundle:PageUser')->findOneBy(array('userId' => $userId, 'pageId' => $request->getPageId()));
            $pageUser->setStatus(PageUser::STATUS_ACTIVE);
            $this->em->persist($pageUser);

            $this->flushNeeded = true;
        } else {
            switch ($request->getObject()) {
                case 'CM\CMBundle\Entity\Event':

                    break;
                case 'CM\CMBundle\Entity\Page':

                    break;
            }
        }
    }

    public function refuseRequest($userId, $options = array())
    {
        $request = $this->em->getRepository('CMBundle:Request')->updateStatus($userId, $options, null, Request::STATUS_REFUSED);

        if (!is_null($request->getEntity())) {
            $entityUser = $this->em->getRepository('CMBundle:EntityUser')->findOneBy(array('userId' => $userId, 'entityId' => $request->getEntityId()));
            $newEntityUserStatus = $entityUser->getStatus() == EntityUser::STATUS_PENDING ? EntityUser::STATUS_REFUSED_ENTITY_USER : EntityUser::STATUS_REFUSED_ADMIN;
            $entityUser->setStatus($newEntityUserStatus);
            $this->em->persist($entityUser);

            $this->em->createQueryBuilder('r')
                ->delete('CMBundle:Request', 'r')
                ->where('r.fromUser = :user_id')->setParameter('user_id', $userId)
                ->andWhere('r.entityId = :entity_id')->setParameter('entity_id', $request->getEntityId())
                ->getQuery()
                ->execute();

            $this->flushNeeded = true;
        } elseif (!is_null($request->getPage())) {
            $pageUser = $this->em->getRepository('CMBundle:PageUser')->findOneBy(array('userId' => $userId, 'pageId' => $request->getPageId()));
            $newPageUserStatus = $pageUser->getStatus() == PageUser::STATUS_PENDING ? PageUser::STATUS_REFUSED_GROUP_USER : PageUser::STATUS_REFUSED_ADMIN;
            $pageUser->setStatus($newPageUserStatus);
            $this->em->persist($pageUser);

            $this->em->createQueryBuilder('r')
                ->delete('CMBundle:Request', 'r')
                ->where('r.fromUser = :user_id')->setParameter('user_id', $userId)
                ->andWhere('r.pageId = :page_id')->setParameter('page_id', $options['pageId'])
                ->getQuery()
                ->execute();

            $this->flushNeeded = true;
        } else {
            switch ($request->getObject()) {
                case 'CM\CMBundle\Entity\Event':

                    break;
                case 'CM\CMBundle\Entity\Page':

                    break;
            }
        }
    }

    public function removeRequests($userId, $options)
    {
        $this->em->getRepository('CMBundle:Request')->delete($userId, $options);
    }
}