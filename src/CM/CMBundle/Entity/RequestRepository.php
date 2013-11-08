<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * RequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class RequestRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'paginate'      => true,
            'limit'         => 25
        ), $options);
    }

    public function getFor($userId, $object, $objectId)
    {
        return $this->createQueryBuilder('r')
            ->select('r')
            ->where('r.userId = :user_id')->setParameter('user_id', $userId)
            ->andWhere('r.object = :object')->setParameter('object', $object)
            ->andWhere('r.objectId = :objectId')->setParameter('objectId', $objectId)
            ->getQuery()->getResult();
    }

    public function getRequests($userId, $direction = 'incoming', array $options = array())
    {
        $options = self::getOptions($options);
        
        $query = $this->createQueryBuilder('r')
            ->select('r, u, e')
            ->leftJoin('r.entity', 'e');
        if ($direction == 'incoming') {
            $query->leftJoin('r.user', 'u');
        } elseif ($direction == 'outgoing') {
            $query->leftJoin('r.fromUser', 'u');
        }
        $query->where('u.id = :id')->setParameter('id', $userId)
            ->andWhere('r.status NOT IN ('.Request::STATUS_ACCEPTED.','.Request::STATUS_REFUSED.')')
            ->orderBy('r.createdAt', 'desc');

        return $options['paginate'] ? $query->getQuery() : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getNumberNew($userId)
    {
        return $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->where('r.userId = :id')->setParameter('id', $userId)
            ->andWhere('r.status = :new')->setParameter('new', Request::STATUS_NEW)
            ->getQuery()->getSingleScalarResult();
    }

    public function updateStatus($userId, $object = null, $objectId = null, $oldStatus = null, $newStatus = Request::STATUS_PENDING)
    {
        $query = $this->createQueryBuilder('r')
            ->update('CMBundle:Request', 'r')
            ->where('r.user = :user_id')->setParameter('user_id', $userId);
        if (!is_null($object)) {
            $query->andWhere('r.object = :object')->setParameter('object', $object)
                ->andWhere('r.objectId = :object_id')->setParameter('object_id', $objectId);
        }
        if (!is_null($oldStatus)) {
            $query->andWhere('r.status = '.$oldStatus);
        }
        $query->set('r.status', $newStatus)
            ->getQuery()
            ->execute();
        if ($newStatus == Request::STATUS_ACCEPTED || $newStatus == Request::STATUS_REFUSED) {
            $request = $this->createQueryBuilder('r')
                ->select('PARTIAL r.{id, object, objectId}')
                ->where('r.userId = :user_id')->setParameter('user_id', $userId)
                ->andWhere('r.object = :object')->setParameter('object', $object)
                ->andWhere('r.objectId = :object_id')->setParameter('object_id', $objectId)
                ->getQuery()->getSingleResult();

            switch ($request->getObject()) {
                case 'Event':
                    $entityUser = $this->getEntityManager()->createQueryBuilder()
                        ->select('PARTIAL eu.{id, status}')
                        ->from('CMBundle:EntityUser', 'eu')
                        ->where('eu.userId = :user_id')->setParameter('user_id', $userId)
                        ->andWhere('eu.entityId = :entity_id')->setParameter('entity_id', $request->getObjectId())
                        ->getQuery()
                        ->getSingleResult();
                        
                    if ($newStatus == Request::STATUS_ACCEPTED) {
                        $this->getEntityManager()->createQueryBuilder()
                            ->update('CMBundle:EntityUser', 'eu')
                            ->where('eu.user = :user_id')->setParameter('user_id', $userId)
                            ->andWhere('eu.entity = :entity_id')->setParameter('entity_id', $objectId)
                            ->set('eu.status', EntityUser::STATUS_ACTIVE)
                            ->getQuery()
                            ->execute();
                    } elseif ($newStatus == Request::STATUS_REFUSED) {
                        $newEntityUserStatus = $entityUser->getStatus() == EntityUser::STATUS_PENDING ? EntityUser::STATUS_REFUSED_ENTITY_USER : EntityUser::STATUS_REFUSED_ADMIN;
                        $this->getEntityManager()->createQueryBuilder()
                            ->update('CMBundle:EntityUser', 'eu')
                            ->where('eu.user = :user_id')->setParameter('user_id', $userId)
                            ->andWhere('eu.entity = :entity_id')->setParameter('entity_id', $objectId)
                            ->set('eu.status', $newEntityUserStatus)
                            ->getQuery()
                            ->execute();
                        $this->createQueryBuilder('r')
                            ->delete('CMBundle:Request', 'r')
                            ->where('r.fromUser = :user_id')->setParameter('user_id', $userId)
                            ->andWhere('r.object = :object')->setParameter('object', $request->getObject())
                            ->andWhere('r.objectId = :object_id')->setParameter('object_id', $request->getObjectId())
                            ->getQuery()
                            ->execute();
                    }
                    break;
            }
        }
    }

    public function delete($userId, $object, $objectId)
    {
        $this->createQueryBuilder('r')
            ->delete('CMBundle:Request', 'r')
            ->where('r.user = :user_id')->setParameter('user_id', $userId)
            ->andWhere('r.object = :object')->setParameter('object', $object)
            ->andWhere('r.objectId = :object_id')->setParameter('object_id', $objectId)
            ->getQuery()
            ->execute();

        $this->getEntityManager()->createQueryBuilder()
            ->delete('CMBundle:EntityUser', 'eu')
            ->where('eu.user = :user_id')->setParameter('user_id', $userId)
            ->andWhere('eu.entity = :entity_id')->setParameter('entity_id', $objectId)
            ->getQuery()
            ->execute();
    }
}
