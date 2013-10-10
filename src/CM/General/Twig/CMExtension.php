<?php

namespace CM\General\Twig;

use Symfony\Component\Translation\Translator;

class CMExtension extends \Twig_Extension
{
    private $translator;

    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'get_class_name' => new \Twig_Filter_Method($this, 'getClassName'),
        );
    }

    public function getFunctions()
    {
        return array(
            'delete_link' => new \Twig_Function_Method($this, 'getDeleteLink'),
        );
    }

    public function ceil($number)
    {
        return ceil($number);
    }

    public function getClassName($object)
    {
        return preg_replace('/^[\w\d_\\\]*\\\/', '', get_class($object));
    }

    public function getDeleteLink($link, $object = 'element', $options = array())
    {
        $options = array_merge(array(
            'object' => 'image',
            'text' => $this->translator('Delete'),
            'data-toggle' => 'popover',
            'data-content' => '<p>'.$this->translator('Are you sure you want to delete this '.$object.'?').'</p><a href="'.$link.' class="btn btn-primary">Confirm <span class="btn popover-close">'.$this->translator('Cancel').'</span>',
            'data-placement' => 'top',
            'title data-original-title' => $this->translator('Delete confirmation'),
            'icon' => 'trash',
            'class' => null,
        ), $options);
      
        $text = $options['text'];
        if ($options['icon']) {
            $text = '<i class="glyphicon glyphicon-'.$options['icon'].'"></i> '.$text;
        }
        unset($options['text'], $options['icon']);
        if (is_null($options['class'])) {
            unset($options['class']);
        }

        return '<a href="'.$link.'" >'.$text.'</a>';
        
        // echo link_to($text, $link, $options);
    } 

    public function getName()
    {
        return 'cm_extension';
    }
}