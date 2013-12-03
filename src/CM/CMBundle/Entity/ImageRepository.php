<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * ImageRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ImageRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'userId'       => null,
            'groupId'      => null,
            'pageId'       => null,
            'type'       => ImageAlbum::TYPE_ALBUM,
            'paginate'      => true,
            'limit'         => 25,
        ), $options);
    }

    public function getImages($options)
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('i')
            ->select('i');
        if (!is_null($options['userId'])) {
            $query->andWhere('i.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('i.pageId is NULL')
                ->andWhere('i.groupId is NULL');
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('i.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $query->andWhere('i.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }
        $query->orderBy('i.main')
            ->addOrderBy('i.sequence');

        return $options['paginate'] ? $query->getQuery() : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getEventsImages($options)
    {
        $options = self::getOptions($options);

        $count = $this->getEntityManager()->createQueryBuilder()
            ->select('count(e.id)')
            ->from('CMBundle:Entity', 'e')
            ->where('e not instance of :image_album')->setParameter('image_album', get_class(new ImageAlbum))
            ->innerJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('e.images', 'i')
            ->andWhere('size(e.images) > 1');
        if (!is_null($options['userId'])) {
            $count->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('p.pageId is NULL')
                ->andWhere('p.groupId is NULL');
        }
        if (!is_null($options['pageId'])) {
            $count->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $count->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('e')
            ->from('CMBundle:Entity', 'e')
            ->where('e not instance of :image_album')->setParameter('image_album', get_class(new ImageAlbum))
            ->innerJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('e.images', 'i')
            ->andWhere('size(e.images) > 1');
        if (!is_null($options['userId'])) {
            $query->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('p.pageId is NULL')
                ->andWhere('p.groupId is NULL');
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $query->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }
        // $query->orderBy('i.main')
        //     ->addOrderBy('i.sequence');

        return $options['paginate'] ? $query->getQuery()->setHint('knp_paginator.count', $count->getQuery()->getSingleScalarResult()) : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }
}
