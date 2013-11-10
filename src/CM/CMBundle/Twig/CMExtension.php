<?php

namespace CM\CMBundle\Twig;

use Symfony\Component\Translation\Translator;
use CM\CMBundle\Service\Helper;
use Symfony\Component\Security\Core\SecurityContext;
use CM\CMBundle\Service\UserAuthentication;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\Notification;

class CMExtension extends \Twig_Extension
{
    private $translator;

    private $helper;

    private $securityContext;

    private $userAuthentication;

    private $options;

    public function __construct(Translator $translator, Helper $helper, SecurityContext $securityContext, UserAuthentication $userAuthentication, $options = array())
    {
        $this->translator = $translator;
        $this->securityContext = $securityContext;
        $this->userAuthentication = $userAuthentication;
        $this->options = array_merge(array(
            'images_abs_dir' => '/',
            'sizes' => array()
        ), $options);
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
            'can_manage' => new \Twig_Function_Method($this, 'getCanManage'),
            'related_object' => new \Twig_Function_Method($this, 'getRelatedObject'),
            'delete_link' => new \Twig_Function_Method($this, 'getDeleteLink'),
            'show_img_box' => new \Twig_Function_Method($this, 'getShowImgBox'),
            'request_tag' => new \Twig_Function_Method($this, 'getRequestTag'),
        );
    }

    public function ceil($number)
    {
        return ceil($number);
    }

    public function getClassName($object)
    {
        $name = new \ReflectionClass($object);
        return $name->getShortName();
    }

    public function getCanManage($object)
    {
        return $this->userAuthentication->canManage($object);
    }

    public function getRelatedObject($entity)
    {
        return $this->helper->getObject($entity);
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

        // Get max dimension for filtered image
        $maxDim = preg_filter(['/\/(?:.(?!\/))+$/', '/^(.*\/)*/'], ['', ''], $img); 

        // Get image dimensions
        $fileName = preg_replace('/^\/.*\//', '', $img);
        $img_size = @getimagesize($this->options['images_dir'].'/'.$fileName);
        $img_w = $img_size[0];
        $img_h = $img_size[1];

        // Default image
        // if (!$img || !file_exists($options['folder'].'\/full/'.$img)) {
        //     if ($options['default']) {
        //         $img = 'default_'.$options['default'].'.jpg';
        //     } else {
        //         return '';
        //     }
        // }

        // Folder && image size
        // foreach ($this->options['sizes'] as $size) {
        //     $folder   = $this->options['images_dir'].'/'.$size.'/';
        //     $img_size = @getimagesize($folder.$img);
        //     $img_w    = $img_size[0];
        //     $img_h    = $img_size[1];
        //     if ($width <= $thumbnail['width'] && $height <= $thumbnail['height'] && $img_w >= $width && $img_h >= $height) {
        //         break;
        //     }
        // }

        
        // // No height
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
        $img_ratio = $img_w / $img_h;
        $box_ratio = $width / $height;
        $ratio = $img_ratio - $box_ratio;
        
        // Image format
        if ($img_ratio == 1) {
            $img_r_w = $width;
            $img_r_h = $height;
        } elseif ($img_ratio > 1) {
            $img_r_h = $height;
            $img_r_w = $height * $img_ratio;
        } elseif ($img_ratio < 1) {
            $img_r_h = $width / $img_ratio;
            $img_r_w = $width;
        }
        
        // Resized image size (checks if the resized height is still high enough, otherwise the resized is based on the height instead of the width)
        // if ($img_h / ($img_w / $width) >= $height) {
        //     $img_r_w = $width;
        //     $img_r_h = intval($img_h / ($img_w / $width)); 
        // } else {
        //     $img_r_h = $height;
        //     $img_r_w = intval($img_w / ($img_h / $height)); 
        // } 
        
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
        if ($img_ratio > 1 && isset($options['offset'])) {
            $img_style[] = 'left: -'.$options['offset'].'%'; 
        } /*
elseif ($img_ratio > 1) {
            $img_style[] = 'left: -'.(abs($width - $img_r_w) / 2).'px';
        } 
*/elseif ($img_ratio < 1 && isset($options['offset'])) {
            $img_style[] = 'top: -'.$options['offset'].'%'; 
        } elseif ($img_ratio < 1) {             
            $img_style[] = 'top: -'.($img_r_h / 10).'px';
        }
        
        // Write <img> tag
        $img = '<img src="'.$img.'" style="';
        foreach ($img_style as $attr) {
            $img .= $attr;
        }
        $img .= '"';
        foreach ($options['img_attributes'] as $key => $attr) {
            $img .= ' '.$key.'="'.$attr.'"';
        }
        $img .= ' />';

        if ($options['img_only']) {
            return $img;
            // return image_tag('/'.$folder.$img, array_merge(array('style' => implode($img_style, ' ')), $options['img_attributes']));
        }

        // Write <div> tag
        $imgBox = '<div';
        foreach ($options['box_attributes'] as $key => $attr) {
            $imgBox .= ' '.$key.'="'.$attr.'"';
        }
        $imgBox .= '><div class="image_box-inner">'.$img.'</div></div>';

        if (is_null($options['link'])) {
            return $imgBox;
        }

        // Write <a> tag
        // $img_box = content_tag('div', image_tag('/'.$folder.$img, array_merge(array('style' => implode($img_style, ' ')), $options['img_attributes'])), $options['box_attributes']);
        $link = '<a href="'.$options['link'].'"';
        foreach ($options['link_attributes'] as $key => $attr) {
            $link .= ' '.$key.'="'.$attr.'"';
        }
        $link .= '>'.$imgBox.'</a>';

        return $link; // link_to($img_box, $options['link'], $options['link_attributes']);
    }

    public function getRequestTag(Request $request)
    {
        $user = $this->securityContext->getToken()->getUser();
        if ($user->getId() == $request->getUser()->getId() /*&& $request->getEntity()->getId()*/ && $user->getId() == $request->getEntity()->getPost()->getUserId()) {
            switch ($request->getObject()) {
                case 'Event':
                    return $this->translator->trans('%user% would like to be added as protagonist to your event %object%.', array('%user%' => $request->getFromUser(), '%object%' => $request->getEntity()->getTitle()));
                    // return __('%user% would like to be added as protagonist to your event %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Disc':
                    // return __('%user% would like to be added as protagonist to your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Article':
                    // return __('%user% would like to be added as protagonist to your article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Group':
                    // return __('%user% would like to join the group %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getRelatedObject(), $this->getRelatedObject()->getLinkShow())));
            }
        } elseif ($user->getId() == $request->getUser()->getId()) {
            switch ($request->getObject()) {
                case 'Event':
                    return $this->translator->trans('%user% would like to add you as protagonist to the event %object%.', array('%user%' => $request->getFromUser(), '%object%' => $request->getEntity()->getTitle()));
                    // return __('%user% would like to add you as protagonist to the event %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Disc':
                    // return __('%user% would like to add you as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Article':
                    // return __('%user% would like to add you as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Group':
                    // return __('%user% would like you to join the group %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getRelatedObject(), $this->getRelatedObject()->getLinkShow())));
            }
        } elseif ($user->getId() == $request->getFromUser()->getId()) {
            switch ($request->getObject()) {
                case 'Event':
                    return "C";
                    // return __('You requested %user% to be added as protagonist to the event %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Disc':
                    // return __('You requested %user% to be added as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Article':
                    // return __('You requested %user% to be added as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Group':
                    // return __('You requested %user% to join the group %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getRelatedObject(), $this->getRelatedObject()->getLinkShow())));
            }
        }
    }

    public function getNotificationTag(Notification $notification)
    {
        $user = $this->securityContext->getToken()->getUser();
        switch ($this->getPost()->getObject().'_'.$this->getType()) {
            case 'disc_protagonist':
                return __('%user% added you as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'disc_protagonist_request_accepted':
                return __('%user% has accepted to be added as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'disc_like':
                return __('%user% likes your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'disc_comment':
                return __('%user% has commented your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'user_like':
                return __('%user% likes your %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to(__('post'), $this->getPost()->getLinkShow())));
            case 'user_fan':
                return __('%user% became your fan.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow())));
            default:
                return 'Case: '.$this->getPost()->getObject().'_'.$this->getType().', PostId: '.$this->getPostId().', From: '.$this->getUserRelatedByFromUserId().', Type: '.$this->getType().', Object: '.get_object($this->getPost()->getObject(), $this->getPost()->getObjectIds());
        }
    }

    public function getName()
    {
        return 'cm_extension';
    }
}