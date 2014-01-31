<?php

namespace CM\CMBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Doctrine\Common\Persistence\ObjectManager;
use CM\CMBundle\Entity\HomepageArchive;
use CM\CMBundle\Entity\HomepageCategory;

class HomepageCategoryToHomepageArchiveTransformer implements DataTransformerInterface
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
     * Transforms an entity to an array collection.
     */
    public function transform( $homepageArchive)
    {
        if (is_null($homepageArchive)) {
            return null;
        }
    
        return $homepageArchive->getCategory();
    }

    /**
     * Transforms an array collection to an entity.
     */
    public function reverseTransform($homepageCategory)
    {
        if (is_null($homepageCategory)) {
            return null;
        }

        $homepageArchive = new HomepageArchive;
        $homepageArchive->setCategory($homepageCategory);
    
        return $homepageArchive;
    }
}