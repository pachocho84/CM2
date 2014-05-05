<?php

namespace CM\CMBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/** @Annotation */
class City extends Constraint
{
    public $message = '{{ value }} is not a valid city.';

    /**
     * {@inheritdoc}
     */
    public function getTargets()
    {
        return Constraint::PROPERTY_CONSTRAINT;
    }

    /**
     * {@inheritdoc}
     */
    public function validatedBy()
    {
        return 'cm_cmbundle.city';
    }
}
