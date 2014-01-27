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
            'albumId' => null,
            'userId'       => null,
            'groupId'      => null,
            'pageId'       => null,
            'next' => null,
            'entityId' => null,
            'type'       => ImageAlbum::TYPE_ALBUM,
            'paginate'      => true,
            'limit'         => 25,
        ), $options);
    }

    public function getImagesByIds($ids, $options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('i')
            ->select('i')
            ->where('i.id in (:ids)')->setParameter('ids', $ids);
        if (!is_null($options['limit'])) {
            $query->setMaxResults($options['limit']);
        }
        $query->orderBy('i.updatedAt', 'desc');

        return $query->getQuery()->getResult();
    }

    public function getImages($options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('i')
            ->select('i');
        if (!is_null($options['albumId'])) {
            $query->andWhere('i.entityId = :album_id')->setParameter('album_id', $options['albumId']);
        }
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
        $query->addOrderBy('i.sequence', 'desc');

        return $options['paginate'] ? $query->getQuery() : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getEntityImages($options = array())
    {
        $options = self::getOptions($options);

        $count = $this->getEntityManager()->createQueryBuilder()
            ->select('count(e.id)')
            ->from('CMBundle:Entity', 'e')
            ->where('e not instance of :image_album')->setParameter('image_album', 'image_album')
            ->innerJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->andWhere('p.object in (:objects)')->setParameter('objects', array(
                'CM\CMBundle\Entity\Entity',
                'CM\CMBundle\Entity\Event',
                'CM\CMBundle\Entity\Biography',
                'CM\CMBundle\Entity\Disc'
            ))
            ->leftJoin('e.images', 'i')
            ->andWhere('size(e.images) > 2');
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
            ->select('e, t, i')
            ->from('CMBundle:Entity', 'e')
            ->leftJoin('e.translations', 't')
            ->where('e not instance of :image_album')->setParameter('image_album', 'image_album')
            ->leftJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->andWhere('p.object in (:objects)')->setParameter('objects', array(
                'CM\CMBundle\Entity\Entity',
                'CM\CMBundle\Entity\Event',
                'CM\CMBundle\Entity\Biography',
                'CM\CMBundle\Entity\Disc'
            ))
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

    public function getImage($id = null, $options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('i')
            ->select('i, e, t, p, l, lu, c, cu')
            ->leftJoin('i.entity', 'e')
            ->leftJoin('e.translations', 't')
            ->leftJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('i.likes', 'l')
            ->leftJoin('l.user', 'lu')
            ->leftJoin('i.comments', 'c')
            ->leftJoin('c.user', 'cu');
        if (!is_null($id)) {
            $query->andWhere('i.id = :id')->setParameter('id', $id);
        }
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
        if (!is_null($options['next'])) {
            $query->andWhere('i.entityId = :entity_id')->setParameter('entity_id', $entityId)
                ->andWhere('i.sequence = :seq')->setParameter('seq', $options['next']);
        }

        return $query->getQuery()->getSingleResult();
    }

    public function getImageWithSocial($id)
    {
        return $this->createQueryBuilder('i')
            ->select('i, c, cu, l, lu')
            ->leftJoin('i.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->leftJoin('i.likes', 'l')
            ->leftJoin('l.user', 'lu')
            ->where('i.id = :id')->setParameter('id', $id)
            ->getQuery()->getSingleResult();
    }

    public function getImageWithComments($id)
    {
        return $this->createQueryBuilder('i')
            ->select('i, c, cu')
            ->leftJoin('i.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->where('i.id = :id')->setParameter('id', $id)
            ->getQuery()->getSingleResult();
    }

    public function getLastByVip($limit)
    {
        return $this->createQueryBuilder('i')
            ->select('i, e, t, p, l, lu, c, cu')
            ->leftJoin('i.entity', 'e')
            ->leftJoin('e.translations', 't')
            ->leftJoin('e.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('p.user', 'pu')
            ->leftJoin('i.likes', 'l')
            ->leftJoin('l.user', 'lu')
            ->leftJoin('i.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->where('pu.vip = '.true)
            ->setMaxResults($limit)
            ->getQuery()->getResult();
        // return MultimediaQuery::create()-> 
        //     init()->                      
        //     where('sfGuardUser.IsActive = ?', 1)->                                        
        //         where('User.Vip = ?', true)->
        //         where('Multimedia.Tipo = ?', 'Video Youtube')->                                
        //     orderByCreatedAt('desc')->    
        //         limit($limit)->
        //         find();
    }  
}
