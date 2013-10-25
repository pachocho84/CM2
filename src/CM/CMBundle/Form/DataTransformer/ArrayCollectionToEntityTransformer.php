<?php

namespace CM\CMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;

class ArrayCollectionToEntityTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;
    
    private $index;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $index = 0)
    {
        $this->om = $om;
        $this->index = $index;
    }

    /**
     * Transforms an array collection to an entity.
     */
    public function transform($arrayCollection)
    {
        if (is_null($arrayCollection)) {
            return null;
        }
    
        return $arrayCollection[$this->index];
    }

    /**
     * Transforms an entity to an array collection.
     */
    public function reverseTransform($entity)
    {
        if (is_null($entity)) {
            return null;
        }
        
        $arrayCollection = new ArrayCollection;
        $arrayCollection[$this->index] = $entity;
    
        return $arrayCollection;
    }
}