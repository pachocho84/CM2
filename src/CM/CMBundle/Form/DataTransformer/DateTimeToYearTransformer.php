<?php

namespace CM\CMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use CM\CMBundle\Service\Helper;

class DateTimeToYearTransformer implements DataTransformerInterface
{
    /**
     * Transforms an array collection to an entity.
     */
    public function transform($dateTime)
    {
        if (is_null($dateTime)) {
            return null;
        }

        return $dateTime->format('Y');
    }

    /**
     * Transforms an entity to an array collection.
     */
    public function reverseTransform($year)
    {
        if (is_null($year)) {
            return null;
        }
        
        $date = new \DateTime;
        return $date->setDate($year, 1, 1);
    }
}