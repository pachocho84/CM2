<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * EventRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class GroupRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
        ), $options);
    }

    public function getAdmins($groupId)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('u')
            ->from('CMBundle:User', 'u')
            ->leftJoin('u.userGroups', 'ug')
            ->where('ug.admin = '.true)
            ->andWhere('identity(ug.group) = :group_id')->setParameter('group_id', $groupId)
            ->getQuery()->getResult();
    }

    public function getCreationPost($groupId, $object) {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('p, g, gu')
            ->from('CMBundle:Post', 'p')
            ->leftJoin('p.group', 'g')
            ->leftJoin('g.groupUsers', 'gu')
            ->where('p.type = '.Post::TYPE_CREATION)
            ->andWhere('g.id = :group_id')->setParameter('group_id', $groupId)
            ->getQuery()->getSingleResult();
    }

    public function getGroupExcludeUsers($groupId, $excludes)
    {
        return $this->createQueryBuilder('g')
            ->select('g, gu, u')
            ->leftJoin('g.groupUsers', 'gu')
            ->leftJoin('gu.user', 'u')
            ->where('g.id = :group_id')->setParameter('group_id', $groupId)
            ->andWhere('u.id NOT IN (:excludes)')->setParameter('excludes', $excludes)
            ->getQuery()->getSingleResult();
    }

    public function filterGroupsForUser($userId)
    {
        return $this->createQueryBuilder('g')
            ->select('g')
            ->leftJoin('g.groupUsers', 'gu')
            ->where('gu.user = :user_id')->setParameter('user_id', $userId);
    }

    public function getGroupsForUser($userId)
    {
        return $this->filterGroupsForUser($userId)->getQuery()->getResult();
    }

    public function getUserIdsFor($groupId, $excludes = array())
    {
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('DISTINCT u.id')
            ->from('CMBundle:User', 'u')
            ->leftJoin('u.userGroups', 'ug')
            ->where('ug.group = :group_id')->setParameter('group_id', $groupId);
        if (count($excludes) > 0) {
            $query->andWhere('u.id NOT IN (:excludes)')->setParameter('excludes', $excludes);
        }
        return array_map(function ($user) { return $user['id']; }, $query->getQuery()->getResult());
    }
}