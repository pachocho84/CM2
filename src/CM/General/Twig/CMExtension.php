<?php

namespace CM\General\Twig;

use Symfony\Component\Translation\Translator;
use CM\CMBundle\Entity\Image;

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
            'show_img_box' => new \Twig_Function_Method($this, 'getShowImgBox'),
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

    public function getShowImgBox($img, $options = array())
    {
        $options = array_merge(array(
            'folder'          => '/images/770',
            'width'           => 150,
            'height'          => null,
            'offset'          => null,
            'default'         => false,
            'link'            => null,
            'link_attributes' => array(),
            'box_attributes'  => array(),
            'img_attributes'  => array(),
            'img_only'        => false,
        ), $options);

        $width  = intval($options['width']);
        $height = intval($options['height']);

        // Default image
        if (!$img || !file_exists('uploads/images/full/'.$img)) {
            if ($options['default']) {
                $img = 'default_'.$options['default'].'.jpg';
            } else {
                return '';
            }
        }

        // Folder && image size
        foreach (Image::$thumbnails as $thumbnail) {
            $folder   = 'uploads'.$thumbnail['dir'].'/';
            $img_size = @getimagesize($folder.$img);
            $img_w    = $img_size[0];
            $img_h    = $img_size[1];
            if ($width <= $thumbnail['width'] && $height <= $thumbnail['height'] && $img_w >= $width && $img_h >= $height) {
                break;
            }
        }
        
        // No height
        if (!$height) { 
            $imgBox = '<div><img src="/'.$folder.$img.' width="'.$width.'"';
            foreach ($options['box_attributes'] as $key => $attr) {
                $imgBox .= ' '.$key.'="'.$attr.'"';
            }
            $imgBox .= ' /></div>';
            // $img_box = content_tag('div', image_tag('/'.$folder.$img, array('width' => $width)), $options['box_attributes']);
            $link = '<a href="'.$options['link'].'"';
            foreach ($options['link_attributes'] as $key => $attr) {
                $link .= ' '.$key.'="'.$attr.'"';
            }
            $link .= '>'.$imgBox.'</a>';
            return is_null($options['link']) ? $img_box : $link; // link_to($img_box, $options['link'], $options['link_attributes']);
        }

        
        // Ratio
        $img_ratio = number_format($img_w / $img_h, 2);
        $box_ratio = number_format($width / $height, 2);
        $ratio = $img_ratio - $box_ratio;
        
        // Image format
        if ($img_w == $img_h) {
            $format = 'square';
        } elseif ($img_w > $img_h) {
            $format = 'landscape';
        } elseif ($img_w < $img_h) {
            $format = 'portrait';
        } 
        
        // Resized image size (checks if the resized height is still high enough, otherwise the resized is based on the height instead of the width)
        if ($img_h / ($img_w / $width) >= $height) {
            $img_r_w = $width;
            $img_r_h = intval($img_h / ($img_w / $width)); 
        } else {
            $img_r_h = $height;
            $img_r_w = intval($img_w / ($img_h / $height)); 
        } 
        
        // Attributes
        $options['box_attributes']['style'] = array_key_exists('style', $options['box_attributes']) ? 'width: '.$width.'px;  height: '.$height.'px; '.$options['box_attributes']['style'] : 'width: '.$width.'px;  height: '.$height.'px;';
        $options['box_attributes']['class'] = array_key_exists('class', $options['box_attributes']) ? 'image_box '.$options['box_attributes']['class'] : 'image_box';
        
        $img_style   = array();
        $img_style[] = 'width: '.$img_r_w.'px;';
        $img_style[] = 'height: '.$img_r_h.'px;';  
        if (array_key_exists('style', $options['img_attributes'])) {
            $img_style[] = $options['img_attributes']['style'];
            unset($options['img_attributes']['style']);
        }
        
        // Align  
        if ($format == 'landscape' && $ratio > 0 && isset($options['offset'])) {
            $img_style[] = 'left: -'.$options['offset'].'%'; 
        }   elseif ($format == 'landscape' && $ratio > 0) {
            $img_style[] = 'left: -'.intval(($img_r_w - $width) / 2).'px'; 
            // $img_style[] = 'left: -'.intval(((($img_r_w - $width) / 2) * 100) / $width).'%'; 
        } elseif ($format == 'landscape' && $img_r_h >= $height && is_null($options['offset'])) {                   
/*      $img_style[] = 'top: -'.intval(($img_r_h - $height) / 2).'px'; */
/*      echo $img_r_h.' - '.$height.' - '.($img_r_h - $height).' - '.(($img_r_h - $height) / 2).' - '.(((($img_r_h - $height) / 2) * 100) / $height); */
            $img_style[] = 'top: -'.intval(((($img_r_h - $height) / 2) * 100) / $height).'%';
        } elseif ($format == 'landscape' && $img_r_h >= $height && !is_null($options['offset'])) {          
            $img_style[] = 'top: -'.$options['offset'].'%';  
        } elseif ($format == 'landscape' && $img_r_h < $height && is_null($options['offset'])) {
            $img_r_w = intval($img_r_w / ($img_r_h / $height));                       
            $img_style[] = 'top: -'.'0';           
        } elseif ($format == 'portrait' && is_null($options['offset']) && (($img_r_h - $img_r_w) > $img_r_h * 0.2)) { // with enough difference between height and witdh                       
            $img_style[] = 'top: -'.'10%';   
        }  elseif ($format == 'portrait' && is_null($options['offset'])) {                 
            $img_style[] = 'top: -'.intval(($img_r_h - $height) / 2).'px'; 
        } elseif ($format == 'portrait' && !is_null($options['offset'])) {               
            $img_style[] = 'top: -'.intval($options['offset'] / ($img_w / $width)).'px';  
        } elseif ($format == 'square') {                               
            $img_style[] = 'top: -'.intval(($img_r_h - $height) / 2).'px'; 
        }
        
        if ($options['img_only']) {
            $img = '<img src="/'.$folder.$img.' width="'.$width.'" style="';
            foreach ($img_style as $key => $attr) {
                $img .= ' '.$key.': '.$attr.'; ';
            }
            $img .= '"';
            foreach ($options['img_attributes'] as $key => $attr) {
                $img .= ' '.$key.'="'.$attr.'"';
            }
            $img .= ' />';
            return $img;
            // return image_tag('/'.$folder.$img, array_merge(array('style' => implode($img_style, ' ')), $options['img_attributes']));
        }
        
        $imgBox = '<div';
        foreach ($options['box_attributes'] as $key => $attr) {
            $imgBox .= ' '.$key.'="'.$attr.'"';
        }
        $imgBox .= '><img src="/'.$folder.$img.' width="'.$width.'" style="';
        foreach ($img_style as $key => $attr) {
            $imgBox .= ' '.$key.': '.$attr.'; ';
        }
        $imgBox .= '"';
        foreach ($options['img_attributes'] as $key => $attr) {
            $imgBox .= ' '.$key.'="'.$attr.'"';
        }
        $imgBox .= ' /></div>';
        // $img_box = content_tag('div', image_tag('/'.$folder.$img, array_merge(array('style' => implode($img_style, ' ')), $options['img_attributes'])), $options['box_attributes']);
        $link = '<a href="'.$options['link'].'"';
        foreach ($options['link_attributes'] as $key => $attr) {
            $link .= ' '.$key.'="'.$attr.'"';
        }
        $link .= '>'.$imgBox.'</a>';
        return is_null($options['link']) ? $img_box : $link; // link_to($img_box, $options['link'], $options['link_attributes']);
    }

    public function getName()
    {
        return 'cm_extension';
    }
}