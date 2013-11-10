<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * RequestRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends BaseRepository
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
        return $this->createQueryBuilder('n')
            ->select('n')
            ->where('n.userId = :user_id')->setParameter('user_id', $userId)
            ->andWhere('n.object = :object')->setParameter('object', $object)
            ->andWhere('n.objectId = :objectId')->setParameter('objectId', $objectId)
            ->getQuery()->getResult();
    }

    public function getNotifications($userId, array $options = array())
    {
        $options = self::getOptions($options);
        
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('n')
            ->from('CMBundle:Notification', 'n')
            ->leftJoin('n.user', 'u')
            ->where('u.id = :id')->setParameter('id', $userId)
            ->orderBy('n.createdAt', 'desc');

        return $options['paginate'] ? $query->getQuery() : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getNumberNew($userId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('count(n.id)')
            ->from('CMBundle:Notification', 'n')
            ->leftJoin('n.user', 'u')
            ->where('u.id = :id')->setParameter('id', $userId)
            ->andWhere('n.status = :new')->setParameter('new', Notification::STATUS_NEW)
            ->getQuery()->getSingleScalarResult();
    }

    public function updateStatus($userId, $object = null, $objectId = null, $oldStatus = null, $newStatus = Notification::STATUS_NOTIFIED)
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->update('CMBundle:Notification', 'n')
            ->where('n.user = :user_id')->setParameter('user_id', $userId);
        if (!is_null($object)) {
            $query->andWhere('n.object = :object')->setParameter('object', $object)
                ->andWhere('n.objectId = :object_id')->setParameter('object_id', $objectId);
        }
        if (!is_null($oldStatus)) {
            $query->andWhere('n.status = '.$oldStatus);
        }
        $query->set('n.status', $newStatus)
        	->getQuery()
            ->execute();
    }

    public function delete($userId, $object, $objectId, $type)
    {
        $this->createQueryBuilder('n')
            ->delete('CMBundle:Notification', 'n')
            ->where('n.fromUser = :user_id')->setParameter('user_id', $userId)
            ->andWhere('n.object = :object')->setParameter('object', $object)
            ->andWhere('n.objectId = :object_id')->setParameter('object_id', $objectId)
            ->andWhere('n.type = '.$type)
            ->getQuery()
            ->execute();
    }
}
