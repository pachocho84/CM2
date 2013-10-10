<?php

namespace CM\General\Twig;

class CMExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('get_class_name', array($this, 'getClassName')),
        );
    }

    public function getClassName($object)
    {
        return preg_replace('/^[\w\d_\\\]*\\\/', '', get_class($object));
    }

    public function getName()
    {
        return 'cm_extension';
    }
}