<?php

namespace CM\CMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Entity\EntityUserTag;
use CM\CMBundle\Model\ArrayContainer;

class TagsToArrayTransformer implements DataTransformerInterface
{
    private $tags;

    private $class;

    /**
     * @param ObjectManager $om
     */
    public function __construct($tags, $class)
    {
        $this->tags = $tags;
        $this->class = $class;
    }

    /**
     * Transforms an array collection to an entity.
     */
    public function transform($tags)
    {
        if (is_null($tags)) {
            return null;
        }

        uasort($tags, function($a, $b) { return $a->getOrder() - $b->getOrder(); });

        return array_map(function($tag) { return $tag->getTagId(); }, $tags->toArray());
    }

    /**
     * Transforms an entity to an array collection.
     */
    public function reverseTransform($array)
    {
        if (is_null($array)) {
            return null;
        }
        
        $tags = array();
        foreach ($array as $order => $id) {
        	$entityUserTag = new $this->class;
        	$entityUserTag->setTag($this->tags[$id])
        		->setOrder($order);
            $tags[] = $entityUserTag;
        }
    
        return $tags;
    }
}