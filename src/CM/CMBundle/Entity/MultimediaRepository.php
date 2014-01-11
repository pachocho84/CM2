<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * MultimediaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MultimediaRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'userId' => null,
            'pageId' => null,
            'groupId' => null,
            'paginate' => true,
            'limit' => null
        ), $options);
    }

    public function getMultimedias($options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('m')
            ->select('m, p, c, cu, l, lu')
            ->leftJoin('m.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->leftJoin('p.likes', 'l')
            ->leftJoin('l.user', 'lu');

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

        if (is_null($options['paginate']) && !is_null($options['limit'])) {
            $query->setMaxResults($options['limit']);
        }
        $query->orderBy('p.createdAt', 'desc');

        return $options['paginate'] ? $query->getQuery() : $query->getQuery()->getResult();
    }

    public function getMultimedia($id, $options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('m')
            ->select('m, p, c, cu, l, lu')
            ->leftJoin('m.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->leftJoin('p.likes', 'l')
            ->leftJoin('l.user', 'lu')
            ->where('m.id = :id')->setParameter('id', $id);

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

        $multimedia = $query->setMaxResults(1)->getQuery()->getResult();
        if (is_array($multimedia) && count($multimedia) > 0) {
            $multimedia = $multimedia[0];
        } else {
            $multimedia = null;
        }
        return $multimedia;
    }

    public function getLastByVip($limit)
    {
        return $this->createQueryBuilder('m')
            ->select('m, p, pu, c, cu, l, lu')
            ->leftJoin('m.posts', 'p', 'with', 'p.type = '.Post::TYPE_CREATION)
            ->leftJoin('p.user', 'pu')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->leftJoin('p.likes', 'l')
            ->leftJoin('l.user', 'lu')
            ->where('pu.vip = '.true)
            ->andWhere('m.type = :type')->setParameter('type', Multimedia::TYPE_YOUTUBE)
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
