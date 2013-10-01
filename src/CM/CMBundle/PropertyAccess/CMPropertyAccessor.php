<?php

namespace CM\CMBundle\PropertyAccess;

use Symfony\Component\PropertyAccess\PropertyAccessor;

class CMPropertyAccessor extends PropertyAccessor
{
    public function __construct($magicCall = true)
    {
        parent::__construct($magicCall);
    }
}