<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * PostRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PostRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'entityId' => null,
            'object' => null,
            'after' => null,
            'userId' => null,
            'pageId' => null,
            'groupId' => null,
            'paginate' => true,
            'limit' => null,
            'objects' => array()
        ), $options);
    }

    public function getEntity($entityId)
    {
        return $this->createQueryBuilder('p')
            ->select('p, e')
            ->leftJoin('p.entity', 'e')
            ->where('e.id = :id')->setParameter('id', $entityId)
            ->getQuery()->getSingleResult();
    }

    public function getLastPosts($options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('p')
            ->select('distinct p, e, t, c, i, eu, ep, epl, epc, eplu, epcu');
        $query->leftJoin('p.entity', 'e')
            ->leftJoin('e.translations', 't')
            ->leftJoin('e.entityCategory', 'c')
            ->leftJoin('e.images', 'i', 'WITH', 'i.main = '.true)
            ->leftJoin('e.entityUsers', 'eu', 'WITH', 'eu.status = '.EntityUser::STATUS_ACTIVE)
            ->leftJoin('e.posts', 'ep')
            ->leftJoin('ep.likes', 'epl')
            ->leftJoin('ep.comments', 'epc')
            ->leftJoin('epl.user', 'eplu')
            ->leftJoin('epc.user', 'epcu');
        if (!is_null($options['entityId'])) {
            $query->andWhere('e.id = :entity_id')->setParameter('entity_id', $options['entityId']);
        }
        if (!is_null($options['object'])) {
            $query->andWhere('p.object = :object')->setParameter('object', $options['object']);
        } elseif (count($options['objects'])) {
            $query->andWhere('p.object in (:objects)')->setParameter('objects', $options['objects']);
        }
        if (!is_null($options['after'])) {
            $query->andWhere('p.updatedAt > :after')->setParameter('after', $options['after']);
        }
        if (!is_null($options['pageId'])) {
            $query->andWhere('p.pageId = :page_id')->setParameter('page_id', $options['pageId']);
        } elseif (!is_null($options['groupId'])) {
            $query->andWhere('p.groupId = :group_id')->setParameter('group_id', $options['groupId']);
        } elseif (!is_null($options['userId'])) {
            $query->andWhere('p.userId = :user_id')
                ->setParameter('user_id', $options['userId']);
        }
        $query->orderBy('p.updatedAt', 'desc')
            ->addOrderBy('p.id', 'desc');
        if (!$options['paginate'] && !is_null($options['limit'])) {
            $query->setMaxResults($options['limit']);
        }

        return $options['paginate'] ? $query->getQuery() : $query->getQuery()->getResult();
    }

    public function getLastPostFor($userId, $type, $object = null, $objectId = null, $options = array())
    {
        $options = self::getOptions($options);

        $query = $this->createQueryBuilder('p')
            ->select('p')
            ->where('identity(p.user) = :user_id')->setParameter('user_id', $userId)
            ->andWhere('p.type = '.$type);
        if (!is_null($options['after'])) {
            $query->andWhere('p.updatedAt > :time')->setParameter('time', $options['after']);
        }
        if (!is_null($object)) {
            $query->andWhere('p.object = :object')->setParameter('object', $object);
        }
        if (!is_null($objectId)) {
            $query->andWhere('p.objectIds like :object_id')->setParameter('object_id', '%,'.$objectId.',%');
        }
        $query->orderBy('p.updatedAt', 'desc');
        if (!is_null($options['limit'])) {
            $query->setMaxResults($options['limit']);
        }
        return $query->getQuery()->getResult();
    }

    // static public function getLastPosts($options = array())
    // {
    //     $options = self::getOptions($options);
        
    //     $query = PostQuery::create()->
    //         init()->
    //         _if($options['user_id'])->
    //             leftJoin('Post.Protagonist')->
    //             condition('owner', 'Post.UserId = ?', $options['user_id'])->
    //             condition('protagonist_user', 'Protagonist.UserId = ?', $options['user_id'])->
    //         condition('protagonist_active', 'Protagonist.Status = ?', 'active')->
    //         combine(array('protagonist_user', 'protagonist_active'), 'and', 'protagonist')->
    //         where(array('owner', 'protagonist'), 'or')->
    //         _endIf()->
    //         groupBy('Post.Id')->
    //         orderByUpdatedAt('desc');
            
    //     return $options['paginate'] ? $query->paginate(sfContext::getInstance()->getRequest()->getParameter('page', 1), $options['per_page']) : $query->limit($options['limit'])->find();
            
    // }

    public function delete($creatorId, $userId, $object, $objectIds, $entityId = null)
    {
        $query = $this->createQueryBuilder('p')
            ->delete('CMBundle:Post', 'p')
            ->where('p.creator = :creator_id')->setParameter('creator_id', $creatorId)
            ->andWhere('p.user = :user_id')->setParameter('user_id', $userId)
            ->andWhere('p.object = :object')->setParameter('object', $object);
        if (!is_null($entityId)) {
            $query->andWhere('p.entity = :entity_id')->setParameter('entity_id', $entityId);
        }
        // TODO: make it usable for images
        $query->getQuery()->execute();
    }

    public function getPostWithComments($id)
    {
        return $this->createQueryBuilder('p')
            ->select('p, c, cu')
            ->leftJoin('p.comments', 'c')
            ->leftJoin('c.user', 'cu')
            ->where('p.id = :id')->setParameter('id', $id)
            ->getQuery()->getSingleResult();
    }
}
