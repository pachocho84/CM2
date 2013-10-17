<?php

namespace CM\CMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use Acme\TaskBundle\Entity\Issue;

class UserTagsToChoiceTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     * @return string
     */
    public function transform($userTags)
    {
        if (null === $userTags) {
            return "";
        }

        $choices = array();
        foreach ($userTags as $userTag) {
            $choices[$userTag->getId()] = $userTag->getName();
        }

        return $choices;
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
    public function reverseTransform($number)
    {
        if (!$number) {
            return null;
        }

        $userTag = $this->om
            ->getRepository('CMBundle:UserTag')
            ->findOneById($number);

        if (null === $userTag) {
            throw new TransformationFailedException('UserTag not found');
        }

        return $userTag;
    }
}