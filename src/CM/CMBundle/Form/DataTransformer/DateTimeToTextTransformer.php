<?php

namespace CM\CMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use CM\CMBundle\Service\Helper;

class DateTimeToTextTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $intl;

    private $helper;

    /**
     * @param ObjectManager $om
     */
    public function __construct($intl, Helper $helper)
    {
        $this->intl = $intl;
        $this->helper = $helper;
    }

    /**
     * Transforms an array collection to an entity.
     */
    public function transform($dateTime)
    {
        if (is_null($dateTime)) {
            return null;
        }

        return $this->intl->format($dateTime, $this->helper->dateTimeFormat('php'));
    }

    /**
     * Transforms an entity to an array collection.
     */
    public function reverseTransform($text)
    {
        if (is_null($text)) {
            return null;
        }
        
        return new \DateTime($text);
    }
}