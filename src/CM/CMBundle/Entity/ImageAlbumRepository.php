<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * ImageAlbumRepository
 *
 * This class was generated ay the Doctrine ORM. Add your own custom
 * repository methods aelow.
 */
class ImageAlbumRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'userId'       => null,
            'groupId'      => null,
            'pageId'       => null,
            'type'       => ImageAlbum::TYPE_ALBUM,
            'after' => null,
            'paginate'      => true,
            'limit'         => 25,
        ), $options);
    }

    public function countAlbumsAndImages($options = array())
    {
        $options = self::getOptions($options);

        $albums = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->leftJoin('a.posts', 'p')
            ->where('p.object = :object')->setParameter('object', get_class(new ImageAlbum));
        $entities = $this->getEntityManager()->createQueryBuilder()
            ->select('count(e.id)')
            ->from('CMBundle:Entity', 'e')
            ->leftJoin('e.posts', 'p')
            ->where('p.object != :object')->setParameter('object', get_class(new ImageAlbum))
            ->andWhere('size(e.images) > 0');
        $images = $this->getEntityManager()->createQueryBuilder()
            ->select('count(i.id)')
            ->from('CMBundle:Image', 'i');
        if (!is_null($options['userId'])) {
            $albums->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('p.pageId is NULL')
                ->andWhere('p.groupId is NULL');
            $entities->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('p.pageId is NULL')
                ->andWhere('p.groupId is NULL');
            $images->andWhere('i.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('i.pageId is NULL')
                ->andWhere('i.groupId is NULL');
        }
        if (!is_null($options['pageId'])) {
            $albums->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
            $entities->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
            $images->andWhere('i.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $albums->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
            $entities->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
            $images->andWhere('i.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }

        return array(
            'albums' => $albums->getQuery()->getSingleScalarResult(),
            'entities' => $entities->getQuery()->getSingleScalarResult(),
            'images' => $images->getQuery()->getSingleScalarResult()
        );
    }

    public function getImageAlbum($options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p');
        if (!is_null($options['userId'])) {
            $query->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId']);
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $query->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }
        $query->andWhere('a.type = :type')->setParameter('type', $options['type']);
        $album = $query->setMaxResults(1)->getQuery()->getResult();
        if (is_array($album) && count($album) > 0) {
            $album = $album[0];
        } else {
            $album = null;
        }
        return $album;
    }

    public function getLastPost($id, $options = array())
    {
        $options = self::getOptions($options);

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('CMBundle:Post', 'p')
            ->leftJoin('p.entity', 'e')
            ->leftJoin('p.entity', 'a', 'with', 'a instance of CMBundle:ImageAlbum')
            ->where('a.id = :id')->setParameter('id', $id);
        if (!is_null($options['userId'])) {
            $query->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId']);
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $query->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }
        if (!is_null($options['after'])) {
            $query->andWhere('p.updatedAt > :time')->setParameter('time', $options['after']);
        }
        $query->andWhere('a.type = :type')->setParameter('type', $options['type'])
            ->orderBy('p.updatedAt', 'desc');
        $post = $query->setMaxResults(1)->getQuery()->getResult();
        if (is_array($post) && count($post) > 0) {
            $post = $post[0];
        } else {
            $post = null;
        }
        return $post;
    }

    public function getAlbums($options = array())
    {
        $options = self::getOptions($options);

        $count = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->innerJoin('a.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION);
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

        $query = $this->createQueryBuilder('a')
            ->select('a')
            ->innerJoin('a.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('a.images', 'i');
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
        $query->orderBy('i.updatedAt', 'desc')
            ->addOrderBy('a.type');

        return $options['paginate'] ? $query->getQuery()->setHint('knp_paginator.count', $count->getQuery()->getSingleScalarResult()) : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getAlbum($id, $options = array())
    {
        $options = self::getOptions($options);

        $query = $this->getEntityManager()->createQueryBuilder('e')
            ->select('e')->from('CMBundle:Entity', 'e')
            ->innerJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->where('e.id = :id')->setParameter('id', $id);
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

        return $query->getQuery()->getSingleResult();
    }

    public function setMain($id, $albumId)
    {
        $this->getEntityManager()->createQueryBuilder('i')
            ->update('CMBundle:Image', 'i')
            ->set('i.main', 0)
            ->where('i.entityId = :entity_id')->setParameter('entity_id', $albumId)
            ->getQuery()->execute();

        $this->getEntityManager()->createQueryBuilder('i')
            ->update('CMBundle:Image', 'i')
            ->set('i.main', 1)
            ->where('i.id = :id')->setParameter('id', $id)
            ->getQuery()->execute();
    }
}
