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
    
    private $callback;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om, $index = 0, $callback = null)
    {
        $this->om = $om;
        $this->index = $index;
        $this->callback = $callback;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($arrayCollection)
    {
        if (is_null($arrayCollection)) {
            return -1;
        }
    
        return $arrayCollection[$index];
    }

    /**
     * Transforms a string (number) to an object (issue).
     *
     * @param  string $number
     *
     * @return Issue|null
     *
     * @throws TransformationFailedException if object (issue) is not found.
     */
    public function reverseTransform($entity)
    {
        if (is_null($entity)) {
            return -1;
        }
x
        
        $arrayCollection = new ArrayCollection;
        $arrayCollection[] = $entity;
    
        return $arrayCollection;
    }
}