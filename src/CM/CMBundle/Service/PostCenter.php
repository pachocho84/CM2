<?php

namespace CM\CMBundle\Service;

use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Post;

class PostCenter
{
    private $em;

    private $flushNeeded = false;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function flushNeeded()
    {
        return $this->flushNeeded;
    }

    public function flushed()
    {
        $this->flushNeeded = false;
    }

    public function getNewPost(
        $creator,
        $user,
        $type = null,
        $object = null,
        array $objectIds = array(),
        $entity = null,
        $page = null,
        $group = null
    )
    {
        $post = new Post;
        $post->setCreator($creator)
        	->setUser($user);
        if (!is_null($type)) {
        	$post->setType($type);
        }
        if (!is_null($entity)) {
            $post->setEntity($entity);
        } elseif (!is_null($object)) {
        	$post->setObject($object)
        		->setObjectIds($objectIds);
        }
        if (!is_null($page)) {
            $post->setPage($page);
        } elseif (!is_null($group)) {
            $post->setGroup($group);
        }

        return $post;
    }

    public function newPost(
        $creator,
        $user,
        $type = null,
        $object = null,
        array $objectIds = array(),
        $entity = null,
        $page = null,
        $group = null
    )
    {
    	$post = $this->getNewPost(
            $creator,
            $user,
            $type,
            $object,
            $objectIds,
            $entity,
            $page,
            $group
        );
        $this->em->persist($post);
        $this->flushNeeded = true;

        return $post;
    }

    public function removePost($creator, $user, $object, $objectIds)
    {
        $this->em->getRepository('CMBundle:Post')->delete($creator, $user, $object, $objectIds);
        $this->flushNeeded = true;
    }
}