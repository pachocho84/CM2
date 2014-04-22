<?php

namespace CM\CMBundle\Model;

class ArrayContainer
{
    private $array = array();

    public function add($mixed)
    {
        $this->array[] = $mixed;

        return $this;
    }

    public function get()
    {
        return $this->array;
    }
}