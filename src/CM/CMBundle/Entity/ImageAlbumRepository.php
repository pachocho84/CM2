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

    public function getImageAlbum($options)
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

    public function getLastPost($id, $options)
    {
        $options = self::getOptions($options);

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p')
            ->from('CMBundle:Post', 'p')
            ->leftJoin('p.entity', 'e')
            ->innerJoin('CMBundle:ImageAlbum', 'a', 'with', 'e.id = a.id')
            ->where('e.discr = :discr')->setParameter('discr', 'image_album')
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

    public function getAlbums($options)
    {
        $options = self::getOptions($options);
        
        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('p, a')
            ->from('CMBundle:Post', 'p')
            ->leftJoin('p.entity', 'e')
            ->innerJoin('CMBundle:ImageAlbum', 'a', 'with', 'e.id = a.id')
            ->where('e.discr = :discr')->setParameter('discr', 'image_album');
        if (!is_null($options['userId'])) {
            $query->andWhere('p.userId = :user_id')->setParameter('user_id', $options['userId']);
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        if (!is_null($options['groupId'])) {
            $query->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        }
        $query->orderBy('a.type');

        return $options['paginate'] ? $query->getQuery() : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getUserImageAlbum($userId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.user', 'u')
            ->where('u.id = :user_id')->setParameter('user_id', $userId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_PROFILE)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getUserCoverImageAlbum($userId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.user', 'u')
            ->where('u.id = :user_id')->setParameter('user_id', $userId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_COVER)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getUseraackgroundImageAlbum($userId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.user', 'u')
            ->where('u.id = :user_id')->setParameter('user_id', $userId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_BACKGROUND)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getGroupImageAlbum($groupId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.group', 'g')
            ->where('g.id = :group_id')->setParameter('group_id', $groupId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_PROFILE)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getGroupCoverImageAlbum($groupId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.group', 'g')
            ->where('g.id = :group_id')->setParameter('group_id', $groupId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_COVER)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getPageImageAlbum($pageId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.page', 'pg')
            ->where('pg.id = :page_id')->setParameter('page_id', $pageId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_PROFILE)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getPageCoverImageAlbum($pageId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.page', 'pg')
            ->where('pg.id = :page_id')->setParameter('page_id', $pageId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_COVER)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }

    public function getPageaackgroundImageAlbum($pageId)
    {
        return $this->createQueryBuilder('a')
            ->select('a')
            ->leftJoin('a.posts', 'p')
            ->leftJoin('p.page', 'pg')
            ->where('pg.id = :page_id')->setParameter('page_id', $pageId)
            ->andWhere('a.type = '.ImageAlbum::TYPE_BACKGROUND)
            ->setMaxResults(1)
            ->getQuery()->getResult();
    }
}
