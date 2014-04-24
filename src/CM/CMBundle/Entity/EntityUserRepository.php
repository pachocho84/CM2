<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class EntityUserRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        $options = array_merge(array(
            'locale'        => 'en'
        ), $options);
        
        return array_merge(array(
            'exclude'       => null,
            'userId'       => null,
            'pageId'       => null,
            'archive'       => null, 
            'categoryId'   => null,
            'currentUserId' => null,
            'mainImageOnly' => false,
            'paginate'      => true,
            'locales'       => array_values(array_merge(array('en' => 'en'), array($options['locale'] => $options['locale']))),
            'protagonists'  => false,
            'limit'         => 25,
        ), $options);
    }

    public function getActiveForEntity($entityId, $options = array())
    {
        $options = self::getOptions($options);

        return $this->createQueryBuilder('eu')
            ->select('eu, eut, t, tt, u')
            ->leftJoin('eu.entityUserTags', 'eut')
            ->leftJoin('eut.tag', 't', '', '', 't.order')
            ->leftJoin('t.translations', 'tt', 'with', 'tt.locale = :locale')->setParameter('locale', $options['locale'])
            ->join('eu.user', 'u')
            ->andWhere('eu.status = :status')->setParameter('status', EntityUser::STATUS_ACTIVE)
            ->andWhere('eu.entityId = :entity_id')->setParameter('entity_id', $entityId)
            ->getQuery()->getResult();
    }
    
    public function delete($userId, $entityId)
    {
        $this->createQueryBuilder('eu')
            ->delete('CMBundle:EntityUser', 'eu')
            ->where('eu.user = :user_id')->setParameter('user_id', $userId)
            ->andWhere('eu.entity = :entity_id')->setParameter('entity_id', $entityId)
            ->getQuery()
            ->execute();
    }
}
