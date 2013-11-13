<?php

namespace CM\CMBundle\Twig;

use Symfony\Component\Translation\Translator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use CM\CMBundle\Service\Helper;
use Symfony\Component\Security\Core\SecurityContext;
use CM\CMBundle\Service\UserAuthentication;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\Post;

class CMExtension extends \Twig_Extension
{
    private $translator;

    private $router;

    private $helper;

    private $securityContext;

    private $userAuthentication;

    private $options;

    public function __construct(Translator $translator, Router $router, Helper $helper, SecurityContext $securityContext, UserAuthentication $userAuthentication, $options = array())
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->helper = $helper;
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
            'class_name' => new \Twig_Filter_Method($this, 'getClassName'),
            'simple_format_text' => new \Twig_Filter_Method($this, 'getSimpleFormatText'),
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
            'notification_tag' => new \Twig_Function_Method($this, 'getNotificationTag'),
            'entity_short_text' => new \Twig_Function_Method($this, 'getEntityShortText'),
            'post_text' => new \Twig_Function_Method($this, 'getPostText'),
            'show_icon' => new \Twig_Function_Method($this, 'getShowIcon'),
        );
    }

    public function ceil($number)
    {
        return ceil($number);
    }

    public function getClassName($object)
    {
        return Helper::className($object);
    }

    public function getSimpleFormatText($text, $options = array())
    {
        return Helper::simple_format_text($text, $options);
    }

    public function getCanManage($object)
    {
        return $this->userAuthentication->canManage($object);
    }

    public function getRelatedObject($object, $objectId)
    {
        return $this->helper->getObject($object, $objectId);
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
        if (!$img_size) {
            return '';
        }
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
            $userLink = $this->router->generate('user_show', array('slug' => $request->getFromUser()->getSlug()));
            $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
            switch ($this->getClassName($request->getObject())) {
                case 'Event':
                    return $this->translator->trans('%user% would like to be added as protagonist to your event %object%.', array('%user%' => '<a href="'.$userLink.'">'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                    // return __('%user% would like to be added as protagonist to your event %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Disc':
                    // return __('%user% would like to be added as protagonist to your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Article':
                    // return __('%user% would like to be added as protagonist to your article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getE.'</a>'ntity(), $this->getEntity()->getLinkShow())));
                case 'Group':
                    // return __('%user% would like to join the group %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getRelatedObject(), $this->getRelatedObject()->getLinkShow())));
            }
        } elseif ($user->getId() == $request->getUser()->getId()) {
            $userLink = $this->router->generate('user_show', array('slug' => $request->getFromUser()->getSlug()));
            $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
            switch ($this->getClassName($request->getObject())) {
                case 'Event':
                    return $this->translator->trans('%user% would like to add you as protagonist to the event %object%.', array('%user%' => '<a href="'.$userLink.'">'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                    // return __('%user% would like to add you as protagonist to the event %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Disc':
                    // return __('%user% would like to add you as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Article':
                    // return __('%user% would like to add you as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
                case 'Group':
                    // return __('%user% would like you to join the group %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getRelatedObject(), $this->getRelatedObject()->getLinkShow())));
            }
        } elseif ($user->getId() == $request->getFromUser()->getId()) {
            $userLink = $this->router->generate('user_show', array('slug' => $request->getUser()->getSlug()));
            $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
            switch ($this->getClassName($request->getObject())) {
                case 'Event':
                    return $this->translator->trans('You requested %user% to be added as protagonist to the event %object%.', array('%user%' => '<a href="'.$userLink.'">'.$entityLink.'">'.$request->getEntity().'</a>'));;
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
        $userLink = $this->router->generate('user_show', array('slug' => $notification->getFromUser()->getSlug()));
        $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
        switch ($this->getClassName($notification->getPost()->getObject()).'_'.$notification->getType()) {
            case 'Event_'.Notification::TYPE_LIKE:
                return $this->translator->trans('%user% likes your event %object%.', array('%user%' => '<a href="'.$userLink.'">'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Event_'.Notification::TYPE_COMMENT:
                return $this->translator->trans('%user% has commented your event %object%.', array('%user%' => '<a href="'.$userLink.'">'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'disc_protagonist':
                // return __('%user% added you as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'disc_protagonist_request_accepted':
                // return __('%user% has accepted to be added as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'disc_like':
                // return __('%user% likes your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'disc_comment':
                // return __('%user% has commented your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getPost()->getEntity(), $this->getLinkShow())));
            case 'user_like':
                // return __('%user% likes your %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to(__('post'), $this->getPost()->getLinkShow())));
            case 'user_'.Post::TYPE_FAN:
                // return __('%user% became your fan.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow())));
            default:
                // return 'Case: '.$notification->getPost()->getObject().'_'.$notification->getType().', PostId: '.$notification->getPostId().', From: '.$notification->getFromUser().', Type: '.$notification->getType().', Object: '.$notification->getObject();
        }
    }

    public function getEntityShortText(Entity $entity, $max = 400, $stripped = false)
    {
        $text = $entity->getExtract() && ($this->securityContext->isGranted('ROLE_ADMIN') || $this->securityContext->isGranted('ROLE_CLIENT')) ? $entity->getExtract() : $entity->getText();
        $text_stripped = strip_tags($text);
        
        if ($stripped) {
            $text = $text_stripped;
        } 
        
        if ($text == '') {
            return false;
        } 
                
        if (strlen($text_stripped) > $max) {
            preg_match("#^.{1,".$max."}(\.|\:|\!|\?)#s", $text_stripped, $matches);
            if (array_key_exists(0, $matches)) {
                $text = rtrim($matches[0], '.:'); 
            } else {
                $text = rtrim(Helper::truncate_text($text_stripped, $max, '', true), ',.;!?:').'...';
            }
        }
        
        return $stripped ? Helper::simple_format_text($text) : Helper::show_text($text);
    }

    public function getPostText(Post $post)
    {
        // $object_page = '@'.$post->getObject().'_index';
        $userLink = $this->router->generate('user_show', array('slug' => $post->getPublisher()->getSlug()));
        switch($this->getClassName($post->getObject()).'_'.$post->getType()) {
            case 'Event_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));;
                $categoryLink  = $this->router->generate('event_category', array('category_slug' => $post->getEntity()->getEntityCategory()->getSlug()));
                return $this->translator->trans('%user% has published the event %object% in %category%', array('%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>', '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>', '%category%' => '<a href="'.$categoryLink.'">'.ucfirst($post->getEntity()->getEntityCategory()->getPlural()).'</a>'));
            case 'Comment_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));;
                return $this->translator->trans('%user% commented on %object%', array('%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>', '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'));
            case 'Like_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));;
                return $this->translator->trans('%user% likes %object%', array('%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>', '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'));
            case 'disc_'.Post::TYPE_CREATION:
                // return __('%user% has published %object% in %entity%.', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%entity%' => link_to(strtolower($post->getEntity()->getCategory()), $post->getEntity()->getLinkCategory()), '%object%' => link_to($post->getEntity(), $object_page)));
            case 'image_album_'.Post::TYPE_CREATION:
                // return __('%user% has created the album %object%.', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%object%' => link_to(__($post->getEntity()), $post->getEntity()->getLinkShow('image_album'))));
            case 'image_album_image_add':
                // return format_number_choice('[1]%user% added a new photo to the album %object%.|(1,+Inf]%user% added %count% new photos to the album %object%.', array(
                //         '%user%'    => link_to($post->getUser(), $post->getUser()->getLinkShow()), 
                //         '%count%' => count($post->getObjectIds()),
                //         '%object%'  => link_to($post->getEntity(), $post->getEntity()->getLinkShow())
                //     ), count($post->getObjectIds()));
            case 'biography_'.Post::TYPE_UPDATE:
                // return __('%user% has updated '.$post->getPublisherSex('his').' %biography%.', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%biography%' => link_to(__('biography'), '@biography_user?user='.$post->getUser()->getUsername())));
            case 'user_'.Post::TYPE_REGISTRATION:
                // return __('%user% registered on Circuito Musica. - '.$post->getPublisherSex('M'), array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow())));
            case 'group_'.Post::TYPE_CREATION:
                // return __('%user% has opened the group %group%', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%group%' => link_to($post->getRelatedObject()->getFirst(), $post->getRelatedObject()->getFirst()->getLinkShow())));
            case 'page_'.Post::TYPE_CREATION:
                // return __('%user% has opened the page %page%', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%page%' => link_to($post->getRelatedObject()->getFirst(), $post->getRelatedObject()->getFirst()->getLinkShow())));
            case 'user_img_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' profile picture.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow())));
            case 'user_cover_img_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' cover picture.', array('%user%'     => link_to($post->getUser(), $post->getUser()->getLinkShow())));
            case 'user_'.Post::TYPE_FAN:
                // return format_number_choice('[1]%user% became fan of %fan1%.|[2]%user% became fan of %fan1% and %fan2%.|[3]%user% became fan of %fan1%, %fan2% and %fan3%.|(3,+Inf]%user% became fan of %fan1%, %fan2% and %count% other people. - '.$post->getPublisherSex('M'), array(
                //         '%user%'    => link_to($post->getUser(), $post->getUser()->getLinkShow()), 
                //         '%count%' => count($post->getObjectIds()) - 3,
                //         '%fan1%'    => link_to($post->getRelatedObject()->get(0), $post->getRelatedObject()->get(0)->getLinkShow()),
                //         '%fan2%'    => count($post->getObjectIds()) > 1 ? link_to($post->getRelatedObject()->get(1), $post->getRelatedObject()->get(1)->getLinkShow()) : null,
                //         '%fan3%'    => count($post->getObjectIds()) > 2 ? link_to($post->getRelatedObject()->get(2), $post->getRelatedObject()->get(2)->getLinkShow()) : null
                //     ), count($post->getObjectIds()));
            case 'page_'.Post::TYPE_FAN:
                // return format_number_choice('[1]%user% became fan of the page %fan1%.|[2]%user% became fan of the pages %fan1% and %fan2%.|[3]%user% became fan of the pages %fan1%, %fan2% and %fan3%.|(3,+Inf]%user% became fan of %fan1%, %fan2% and %count%. - '.$post->getPublisherSex('M'), array(
                //         '%user%'    => link_to($post->getUser(), $post->getUser()->getLinkShow()), 
                //         '%count%' => link_to(__('%number% other pages', array('%number%' => count($post->getObjectIds()) - 3)), '@post_show?post_id='.$post->getId(), array('class' => 'show-all')),
                //         '%fan1%'    => link_to($post->getRelatedObject()->get(0), $post->getRelatedObject()->get(0)->getLinkShow()),
                //         '%fan2%'    => count($post->getObjectIds()) > 1 ? link_to($post->getRelatedObject()->get(1), $post->getRelatedObject()->get(1)->getLinkShow()) : null,
                //         '%fan3%'    => count($post->getObjectIds()) > 2 ? link_to($post->getRelatedObject()->get(2), $post->getRelatedObject()->get(2)->getLinkShow()) : null
                //     ), count($post->getObjectIds()));
            case 'group_'.Post::TYPE_FAN:
                // return format_number_choice('[1]%user% became fan of the group %fan1%.|[2]%user% became fan of the groups %fan1% and %fan2%.|[3]%user% became fan of the groups %fan1%, %fan2% and %fan3%.|(3,+Inf]%user% became fan of %fan1%, %fan2% and %count%. - '.$post->getPublisherSex('M'), array(
                //         '%user%'    => link_to($post->getUser(), $post->getUser()->getLinkShow()), 
                //         '%count%' => link_to(__('%number% other groups', array('%number%' => count($post->getObjectIds()) - 3)), '@post_show?post_id='.$post->getId(), array('class' => 'show-all')),
                //         '%fan1%'    => link_to($post->getRelatedObject()->get(0), $post->getRelatedObject()->get(0)->getLinkShow()),
                //         '%fan2%'    => count($post->getObjectIds()) > 1 ? link_to($post->getRelatedObject()->get(1), $post->getRelatedObject()->get(1)->getLinkShow()) : null,
                //         '%fan3%'    => count($post->getObjectIds()) > 2 ? link_to($post->getRelatedObject()->get(2), $post->getRelatedObject()->get(2)->getLinkShow()) : null
                //     ), count($post->getObjectIds()));
            case 'education_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %studies%.', array('%user%'     => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%studies%' => link_to(__('studies'), '@work_education_user?user='.$post->getUser()->getUsername())));
            case 'education_masterclass_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %masterclasses%.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%masterclasses%' => link_to(__('masterclasses'), '@work_education_user?user='.$post->getUser()->getUsername())));
            case 'job_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %works%.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%works%' => link_to(__('works'), '@work_education_user?user='.$post->getUser()->getUsername())));
            default:
                return $post->getObject();
        }
    }

    function getShowIcon($object)
    {
        switch ($object) {
            case 'Event':
            case 'Event_'.Post::TYPE_CREATION:
              return '<i class="glyphicon glyphicon-calendar"></i>';
            case 'Disc':
            case 'Disc_'.Post::TYPE_CREATION:
              return '<i class="glyphicon glyphicon-headphones"></i>';
            case 'Article':
            case 'Article_'.Post::TYPE_CREATION:
              return '<i class="glyphicon glyphicon-print"></i>';
            case 'Link':
            case 'Link_'.Post::TYPE_CREATION:
              return '<i class="glyphicon glyphicon-bookmark"></i>';
            case 'Image':
              return '<i class="glyphicon glyphicon-picture"></i>';
            case 'Multimedia':
              return '<i class="glyphicon glyphicon-film"></i>';
            case 'Page':
            case 'Page_'.Post::TYPE_CREATION:
              return '<i class="glyphicon-bank"></i>';
            case 'Group':
            case 'Group_'.Post::TYPE_CREATION:
              return '<i class="glyphicon-group"></i>';
            case 'Fan':
            case 'User_'.Post::TYPE_FAN:
            case 'Group_'.Post::TYPE_FAN:
            case 'Page_'.Post::TYPE_FAN:
              return '<i class="glyphicon glyphicon-flag"></i>';
            case 'User':
              return '<i class="glyphicon glyphicon-user"></i>';
            case 'User_'.Post::TYPE_REGISTRATION:
              return '<i class="glyphicon-user-add"></i>';
            case 'Biography':
            case 'Biography_'.Post::TYPE_UPDATE:
              return '<i class="glyphicon glyphicon-book"></i>';
            case 'Like':
              return '<i class="glyphicon glyphicon-thumbs-up"></i>';
            case 'Comment':
              return '<i class="glyphicon glyphicon-comment"></i>';
            case 'Wall':
            case 'Post':
              return '<i class="glyphicon glyphicon-th-list"></i>';
            case 'Tag':
              return '<i class="glyphicon glyphicon-tag"></i>';
            case 'Tags':
              return '<i class="glyphicon glyphicon-tags"></i>';
            case 'Work_'.Post::TYPE_EDUCATION:
            case 'Work':
            case 'Job':
            case 'Job_'.Post::TYPE_UPDATE:
              return '<i class="glyphicon glyphicon-briefcase"></i>';
            case 'Education':
            case 'Education_'.Post::TYPE_UPDATE:
              return '<i class="glyphicon-book-open"></i>';
            default:
              return false;
        }
    }

    public function getName()
    {
        return 'cm_extension';
    }
}