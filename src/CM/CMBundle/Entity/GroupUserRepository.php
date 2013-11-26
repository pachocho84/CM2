<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupUserRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'paginate'      => true,
            'limit'         => 25,
        ), $options);
    }

    public function getMembers($groupId, $options = array())
    {
        $options = self::getOptions($options);
        
        $query = $this->createQueryBuilder('gu')
            ->select('gu')
            ->leftJoin('gu.user', 'u')
            ->leftJoin('gu.group', 'g')
            ->where('gu.status in ('.GroupUser::STATUS_ACTIVE.','.GroupUser::STATUS_PENDING.')')
            ->andWhere('gu.groupId = :group_id')->setParameter('group_id', $groupId)
            ->andWhere('gu.userId != g.creatorId')
            ->orderBy('gu.admin', 'desc');

        return $options['paginate'] ? $query->getQuery() : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function updateUserTags($id, array $userTags)
    {  
        $this->createQueryBuilder('gu')
            ->update('CMBundle:GroupUser', 'gu')
            ->where('gu.id = :id')->setParameter('id', $id)
            ->set('gu.userTags', '\''.implode(',', $userTags).'\'')
            ->getQuery()->execute();
    }

    public function delete($userId, $groupId)
    {
        $this->createQueryBuilder('gu')
            ->delete('CMBundle:GroupUser', 'gu')
            ->where('gu.user = :user_id')->setParameter('user_id', $userId)
            ->andWhere('gu.group = :group_id')->setParameter('group_id', $groupId)
            ->getQuery()
            ->execute();
    }
}
