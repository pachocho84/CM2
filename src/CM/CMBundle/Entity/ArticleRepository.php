<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * ArticleRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ArticleRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        $options = array_merge(array(
            'locale'        => 'en'
        ), $options);

        return array_merge(array(
            'userId'       => null,
            'pageId'       => null,
            'paginate'      => true,
            'categoryId' => null,
            'locales'       => array_values(array_merge(array('en' => 'en'), array($options['locale'] => $options['locale']))),
            'protagonist'  => null,
            'protagonists'  => false,
            'status' => null,
            'limit'         => 25,
        ), $options);
    }
    
    public function getArticles(array $options = array())
    {
        $options = self::getOptions($options);
                 
        $count = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->leftJoin('a.posts', 'p', 'WITH', 'p.type = '.Post::TYPE_CREATION.' AND p.object = :object')
            ->setParameter('object', Article::className());

        $query = $this->createQueryBuilder('a')
            ->select('a, t, i, p, l, c, u, lu, cu, pg'.($options['protagonists'] ? ', eu, us' : ''))
            ->leftJoin('a.translations', 't', 'with', 't.locale IN (:locales)')->setParameter('locales', $options['locales'])
            ->setParameter('locales', $options['locales'])
            ->leftJoin('a.images', 'i', 'WITH', 'i.main = '.true)
            ->leftJoin('a.posts', 'p', 'WITH', 'p.type = '.Post::TYPE_CREATION.' AND p.object = :object')
            ->setParameter('object', Article::className())
            ->leftJoin('p.likes', 'l')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('p.user', 'u')
            ->leftJoin('l.user', 'lu')
            ->leftJoin('c.user', 'cu')
            ->leftJoin('p.page', 'pg')
            ->where('t.locale in (:locales)')->setParameter('locales', $options['locales']);
            
        if ($options['protagonists']) {
            $query->leftJoin('a.entityUsers', 'eu', 'eu.userId')
                ->leftJoin('eu.user', 'us');
        }
        
        if ($options['userId']) {
            $count->join('d.entityUsers', 'eu', '', '', 'eu.userId')
                ->andWhere('eu.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('eu.status = '.EntityUser::STATUS_ACTIVE);
            $query->join('d.entityUsers', 'eu', '', '', 'eu.userId')
                ->andWhere('eu.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('eu.status = '.EntityUser::STATUS_ACTIVE);
        }
        
        if ($options['pageId']) {
            $count->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }
        
        if ($options['categoryId']) {
            $count->andWhere('a.category = :category_id')
                ->setParameter(':category_id', $options['categoryId']);
            $query->andWhere('a.category = :category_id')
                ->setParameter(':category_id', $options['categoryId']);
        }

        $query->orderBy('p.createdAt', 'desc');
        
        return $options['paginate'] ? $query->getQuery()->setHint('knp_paginator.count', $count->getQuery()->getSingleScalarResult()) : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function getArticle($id, array $options = array())
    {
        $options = self::getOptions($options);
        
        $query = $this->createQueryBuilder('a')
            ->select('a, t, ec, ect, i, p, pl, plu, pc, pcu, u, pg')
            ->join('a.translations', 't', 'with', 't.locale IN (:locales)')->setParameter('locales', $options['locales'])
            ->join('a.category', 'ec')
            ->join('ec.translations', 'ect', 'with', 'ect.locale = :locale')->setParameter('locale', $options['locale'])
            ->leftJoin('a.image', 'i')
            ->join('a.post', 'p')
            ->leftJoin('p.likes', 'pl')
            ->leftJoin('pl.user', 'plu')
            ->leftJoin('p.comments', 'pc')
            ->leftJoin('pc.user', 'pcu')
            ->join('p.user', 'u')
            ->leftJoin('p.page', 'pg')
            ->andWhere('a.id = :id')->setParameter('id', $id);
        if (!is_null($options['slug'])) {
            $query->andWhere('t.slug = :slug')->setParameter('slug', $options['slug']);
        }
        if ($options['protagonists']) {
            $query->addSelect('eu, us');
            if (is_array($options['status'])) {
                $query->join('a.entityUsers', 'eu', 'with', 'eu.status in (:status)', 'eu.userId')
                    ->setParameter('status', $options['status']);
            } elseif (!is_null($options['status'])) {
                $query->join('a.entityUsers', 'eu', 'with', 'eu.status = :status', 'eu.userId')
                    ->setParameter('status', $options['status']);
            } else {
                $query->join('ep.entityUsers', 'eu', '', '', 'eu.userId');
            }
            $query->join('eu.user', 'us')
                ->addOrderBy('us.firstName');
        }
        if (!is_null($options['protagonist'])) {
            $query->addSelect('eu')
                ->leftJoin('a.entityUsers', 'eu', 'with', 'eu.userId = :user_id', 'eu.userId')
                ->setParameter('user_id', $options['protagonist']);
        }
        if ($options['tags']) {
            $query->addSelect('eut, ta, tat')
                ->leftJoin('eu.entityUserTags', 'eut', '', '', 'eut.order')
                ->leftJoin('eut.tag', 'ta')
                ->leftJoin('ta.translations', 'tat', 'with', 'tat.locale = :locale');
        }
        return $query->getQuery()->getSingleResult();
    }

    public function getLatests($options = array())
    {
        $options = self::getOptions($options);
        
        $count = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->join('a.post', 'p');

        $query = $this->createQueryBuilder('a')
            ->select('a, t, p, i')
            ->leftJoin('a.translations', 't', 'with', 't.locale IN (:locales)')->setParameter('locales', $options['locales'])
            ->join('a.post', 'p')
            ->leftJoin('a.image', 'i')
            ->orderBy('p.createdAt', 'desc');
        if (!is_null($options['exclude'])) {
            $count->andWhere('a.id != :exclude')->setParameter('exclude', $options['exclude']);
            $query->andWhere('a.id != :exclude')->setParameter('exclude', $options['exclude']);
        }
        if (!is_null($options['userId'])) {
            $count->join('a.entityUsers', 'eu', '', '', 'eu.userId')
                ->andWhere('eu.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('eu.status = '.EntityUser::STATUS_ACTIVE);
            $query->join('a.entityUsers', 'eu', '', '', 'eu.userId')
                ->andWhere('eu.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('eu.status = '.EntityUser::STATUS_ACTIVE);
        }
        if (!is_null($options['pageId'])) {
            $count->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        }

        return $options['paginate'] ? $query->getQuery()->setHint('knp_paginator.count', $count->getQuery()->getSingleScalarResult()) : $query->setMaxResults($options['limit'])->getQuery()->getResult();
    }

    public function countLatests($options = array())
    {
        $options = self::getOptions($options);
        
        $query = $this->createQueryBuilder('a')
            ->select('count(a.id)')
            ->join('a.post', 'p')
            ->orderBy('p.createdAt', 'desc');
        if (!is_null($options['userId'])) {
            $query->join('a.entityUsers', 'eu', '', '', 'eu.userId')
                ->andWhere('eu.userId = :user_id')->setParameter('user_id', $options['userId'])
                ->andWhere('eu.status = '.EntityUser::STATUS_ACTIVE);
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('p.pageId = :page_id')
                ->setParameter('page_id', $options['pageId']);
        }

        return $query->getQuery()
            ->getSingleScalarResult();
    }

    public function getSponsored(array $options = array())
    {   
        $options = self::getOptions($options);
        
        $sponsored = $this->getEntityManager()->createQueryBuilder()
            ->select('partial s.{id, entityId, views, start, end}, partial a.{id}')
            ->from('CMBundle:Sponsored','s')
            ->join('s.entity', 'a', 'with', 'a instance of '.Article::className())
            ->andWhere('s.start <= :now')
            ->andWhere('s.end >= :now')
            ->setParameter(':now', new \DateTime)
            ->getQuery()
            ->getResult();

        if (count($sponsored) == 0) return null;

        shuffle($sponsored);
        $sponsored = array_slice($sponsored, 0, $options['limit']);
        
        $this->getEntityManager()->createQueryBuilder()
            ->update('CMBundle:Sponsored', 's')
            ->set('s.views', 's.views + 1')
            ->where('s.id in (:ids)')->setParameter('ids', array_map(function($i) { return $i->getId(); }, $sponsored))
            ->getQuery()->execute();

        return $this->createQueryBuilder('a')
            ->select('a, t, i')
            ->leftJoin('a.translations', 't', 'with', 't.locale IN (:locales)')->setParameter('locales', $options['locales'])
            ->leftJoin('a.image', 'i')
            ->andWhere('a.id IN (:ids)')->setParameter('ids', array_map(function($i) { return $i->getEntityId(); }, $sponsored))
            ->getQuery()
            ->getResult();
    }
}