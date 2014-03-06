<?php

namespace CM\CMBundle\Twig;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use CM\CMBundle\Service\Helper;
use Symfony\Component\Security\Core\SecurityContext;
use CM\CMBundle\Service\UserAuthentication;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\Disc;
use CM\CMBundle\Entity\ImageAlbum;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\EntityUser;
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

    private $imagineFilter;

    private $options;
    
    private $request;
    
    private $environment;

    private $controllerName;

    private $actionName;


    public function __construct(
        Translator $translator,
        Router $router,
        RequestStack $requestStack,
        Helper $helper,
        SecurityContext $securityContext,
        UserAuthentication $userAuthentication,
        CacheManager $imagineFilter,
        $options = array()
    )
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->request = $requestStack->getCurrentRequest();
        $this->helper = $helper;
        $this->securityContext = $securityContext;
        $this->userAuthentication = $userAuthentication;
        $this->imagineFilter = $imagineFilter;
        $this->options = array_merge(array(
            'images_abs_dir' => '/',
            'sizes' => array()
        ), $options);
    }
	
	public function initRuntime(\Twig_Environment $environment)
	{
		$this->environment = $environment;
	}

    public function getFilters()
    {
        return array(
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'class_name' => new \Twig_Filter_Method($this, 'getClassName'),
            'simple_format_text' => new \Twig_Filter_Method($this, 'getSimpleFormatText'),
            'show_text' => new \Twig_Filter_Method($this, 'getShowText'),
            'default_img' => new \Twig_Filter_Method($this, 'getDefaultImg'),
        );
    }

    public function getFunctions()
    {
        return array(
	        'controller_name' => new \Twig_Function_Method($this, 'getControllerName'),
            'action_name' => new \Twig_Function_Method($this, 'getActionName'),
            'datetime_format' => new \Twig_Function_Method($this, 'getDateTimeFormat', array('is_safe' => array('html'))),
            'can_manage' => new \Twig_Function_Method($this, 'getCanManage'),
            'is_admin' => new \Twig_Function_Method($this, 'getIsAdmin'),
            'related_object' => new \Twig_Function_Method($this, 'getRelatedObject'),
            'delete_link' => new \Twig_Function_Method($this, 'getDeleteLink', array('is_safe' => array('html'))),
            'entity_short_text' => new \Twig_Function_Method($this, 'getEntityShortText', array('is_safe' => array('html'))),
            'user_box' => new \Twig_Function_Method($this, 'getUserBox', array('is_safe' => array('html'))),
            'show_img_box' => new \Twig_Function_Method($this, 'getShowImgBox', array('is_safe' => array('html'))),
            'request_tag' => new \Twig_Function_Method($this, 'getRequestTag', array('is_safe' => array('html'))),
            'request_update' => new \Twig_Function_Method($this, 'getRequestUpdate', array('is_safe' => array('html'))),
            'notification_tag' => new \Twig_Function_Method($this, 'getNotificationTag', array('is_safe' => array('html'))),
            'post_text' => new \Twig_Function_Method($this, 'getPostText', array('is_safe' => array('html'))),
            'entity_post_text' => new \Twig_Function_Method($this, 'getEntityPostText', array('is_safe' => array('html'))),
            'icon' => new \Twig_Function_Method($this, 'getIcon', array('is_safe' => array('html'))),
            'tooltip' => new \Twig_Function_Method($this, 'getTooltip', array('is_safe' => array('html'))),
            'modal' => new \Twig_Function_Method($this, 'getModal', array('is_safe' => array('html'))),
            'vimeoImage' => new \Twig_Function_Method($this, 'getVimeoImage', array('is_safe' => array('html'))),
            'soundcloudImage' => new \Twig_Function_Method($this, 'getSoundcloudImage', array('is_safe' => array('html'))),
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

    /**
     * Returns +text+ transformed into html using very simple formatting rules
     * Surrounds paragraphs with <tt>&lt;p&gt;</tt> tags, and converts line breaks into <tt>&lt;br /&gt;</tt>
     * Two consecutive newlines(<tt>\n\n</tt>) are considered as a paragraph, one newline (<tt>\n</tt>) is
     * considered a linebreak, three or more consecutive newlines are turned into two newlines
     */
    public function getSimpleFormatText($text, $options = array())
    {
        $css = (isset($options['class'])) ? ' class="'.$options['class'].'"' : '';

        $text = Helper::pregtr($text, array(
            "/(\r\n|\r)/" => "\n",            // lets make them newlines crossplatform
            "/\n{2,}/"    => "</p><p$css>" // turn two and more newlines into paragraph
        ));

        // turn single newline into <br/>
        $text = str_replace("\n", "\n<br />", $text);
        return '<p'.$css.'>'.$text.'</p>'; // wrap the first and last line in paragraphs before we're done
    }

    /**
     * show_text function.
     *
     * @access public
     * @param mixed $text
     * @return void
     */
    function getShowText($text)
    {
        $actual_length = strlen($text);
        $stripped_length = strlen(strip_tags($text));

        if ($actual_length != $stripped_length) {
            return $text;
        } else {
            return $this->getSimpleFormatText($text);
        }
    }

    function getDefaultImg($image, $options = array())
    {
        $options = array_merge(array(
            'default' => 'default.png',
            'path' => ''
        ), $options);

        if (is_null($image) || empty($image)) {
            $image = $options['default'];
        }

        return $options['path'].$image;
    }
    
    /**
     * Get current controller name
     */
    public function getControllerName()
    {
        if (is_null($this->controllerName)) {
            $pattern = "/Controller\\\([a-zA-Z]*)Controller/";
            $matches = array();
            preg_match($pattern, $this->request->get('_controller'), $matches);
            
            $this->controllerName = strtolower($matches[1]);
        }

        return $this->controllerName;
    }
    
    /**
     * Get current action name 
     */
    public function getActionName()
    {
        if (is_null($this->actionName)) {
            $pattern = "/::([a-zA-Z]*)Action/";
            $matches = array();
            preg_match($pattern, $this->request->get('_controller'), $matches);
            
            $this->actionName = strtolower($matches[1]);
        }

        return $this->actionName;
    }

    public function getDateTimeFormat($lang = 'js')
    {
        return $this->helper->dateTimeFormat($lang);
    }

    public function getCanManage($object)
    {
        return $this->userAuthentication->canManage($object);
    }

    public function getIsAdmin($object)
    {
        return $this->userAuthentication->isAdminOf($object);
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
            $text = '<span class="glyphicon glyphicon-'.$options['icon'].'"></span> '.$text;
        }
        unset($options['text'], $options['icon']);
        if (is_null($options['class'])) {
            unset($options['class']);
        }

        return '<a href="'.$link.'" >'.$text.'</a>';

        // echo link_to($text, $link, $options);
    }

    public function getEntityShortText(Entity $entity, $options = array())
    {
        $options = array_merge(array(
            'max' => 400,
            'stripped' => false,
            'more' => false,
            'moreText' => 'show more',
            'moreTextAlt' => 'show less'
        ), $options);

        if (!$options['more'] && $entity->getExtract() && ($this->securityContext->isGranted('ROLE_ADMIN') || $this->securityContext->isGranted('ROLE_CLIENT'))) {
            $text = $entity->getExtract();
        } else {
            $text = $entity->getText();
        }

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
                $text = rtrim($matches[0], '.:').'.';
            } else {
                $text = rtrim(Helper::truncate_text($text_stripped, $max, '', true), ',.;!?:');
                if (!$options['more']) {
                    $text .= '...';
                }
            }
        }

        $text = $stripped ? $this->getSimpleFormatText($text) : $this->getShowText($text);

        if ($options['more']) {
            $text .= ' <div id="show_more-entity_'.$entity->getId().'">'.substr($entity->getText(), strlen($text)).'</div>';
            // $text .= '<a href="show_more-entity_'.$entity->getId().'" class="box-collapser collapsed" data-toggle="collapse"'.' text-alt="'+ $this->translator->trans($options['moreTextAlt']).'">'.$this->translator->trans($options['moreText']).'</a>';
        }

        return $text;
    }

    public function getUserBox($publisher, $options = array())
    {
        if (!$publisher instanceof User) {
            return '';
        }

        $options = array_merge(array(
        ), $options);

        // var_dump($this->twig->getFilter('e')());die;

        // $img = $this->imagineFilter->getBrowserPath($publisher->getImg(), 50, false);
        // $imgBox = $this->getShowImgBox($img, array('width' => 35, 'height' => 35, 'offset' => $publisher->getImgOffset(), 'box_attributes' => array('class' => 'pull-left')));

        return 'user-popover data-title="'.$publisher.'" data-href="'.$this->router->generate('user_popover', array('slug' => $publisher->getSlug())).'"';
        // $this->getShowImgBox($img, array('width' => 35, 'height' => 35, 'offset' => $publisher->getImgOffset(), 'box_attributes' => array('class' => 'pull-left')))
    }

    public function getShowImgBox($img, $options = array())
    {
        $options = array_merge(array(
            'width'           => 150,
            'height'          => null,
            'offset'          => null,
            'default'         => false,
            'link'            => null,
            'box_attributes'  => array(),
            'img_attributes'  => array(),
            'user_box' => null
        ), $options);

        $width  = $options['width'];
        $height = is_null($options['height']) ? $width : $options['height'];

        // Get image dimensions
        $fileName = preg_replace('/^\/.*\//', '', $img);
        $imageFileSize = @getimagesize($this->options['images_dir'].'/'.$fileName);
        if (!$imageFileSize) {
            return '';
        }

        // Ratio
        $imageRatio = $imageFileSize[0] / $imageFileSize[1];
        $boxRatio = $width / $height;
        $ratio = $imageRatio / $boxRatio;

        // Image format
        if ($ratio == 1) {
            $imageResizedWidth = $width;
            $imageResizedHeight = $height;
        } elseif ($ratio > 1) {
            $imageResizedHeight = $height;
            $imageResizedWidth = $height * $imageRatio;
        } elseif ($ratio < 1) {
            $imageResizedHeight = $width / $imageRatio;
            $imageResizedWidth = $width;
        }

        // Offset
        $imgStyle = array();
        if (is_null($options['offset'])) {
            if ($ratio > 1) { // landscape
                $imgStyle[] = 'left: -'.(($imageResizedWidth - $width) / 2).'px';
            } elseif ($ratio < 1) { // portrait
                $imgStyle[] = 'top: -'.(min($imageResizedHeight - $height, $imageResizedHeight / 10)).'px';
            }
        } else {
            $imgStyle[] = ($ratio > 1 ? 'left' : 'top').': -'.$options['offset'].'%';
        }

        // Attributes
        $options['box_attributes']['style'] = 'width: '.$width.'px;  height: '.$height.'px;'.(array_key_exists('style', $options['box_attributes']) ? ' '.$options['box_attributes']['style'] : '');
        $options['box_attributes']['class'] = 'image_box'.(array_key_exists('class', $options['box_attributes']) ? ' '.$options['box_attributes']['class'] : '');

        $imgStyle[] = 'width: '.$imageResizedWidth.'px';
        $imgStyle[] = 'height: '.$imageResizedHeight.'px';
        if (array_key_exists('style', $options['img_attributes'])) {
            $imgStyle[] = $options['img_attributes']['style'];
            unset($options['img_attributes']['style']);
        }

        // Write img tag
        $imgTag = '<img src="'.$img.'" style="';
        foreach ($imgStyle as $attr) {
            $imgTag .= $attr.'; ';
        }
        $imgTag .= '"';
        foreach ($options['img_attributes'] as $key => $attr) {
            $imgTag .= ' '.$key.'="'.$attr.'"';
        }
        $imgTag .= ' />';

        // Write box tag
        if (!is_null($options['link'])) {
            $boxTag = '<a href="'.$options['link'].'"';
            $boxTagEnd = '</a>';
        } else {
            $boxTag = '<div';
            $boxTagEnd = '</div>';
        }
        foreach ($options['box_attributes'] as $key => $attr) {
            $boxTag .= ' '.$key.'="'.$attr.'"';
        }

        if (!is_null($options['user_box'])) {
            $boxTag = '<div '.$this->getUserBox($options['user_box']).'>'.$boxTag;
            $boxTagEnd = '</div>'.$boxTagEnd;
        }
        
        return $boxTag.'>'.$imgTag.$boxTagEnd;
    }

    public function getRequestTag(Request $request)
    {
        $user = $this->securityContext->getToken()->getUser();

        if ($user->getId() == $request->getUser()->getId()) {
            $userLink = $this->router->generate('user_show', array('slug' => $request->getFromUser()->getSlug()));
            $userBox = $this->getUserBox($request->getFromUser());
            if (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Event' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Event') {
                $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Disc' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('disc_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to be added as protagonist to your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Disc') {
                $entityLink = $this->router->generate('disc_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to add you as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Multimedia' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('multimedia_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your multimedia %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Multimedia') {
                $entityLink = $this->router->generate('multimedia_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the multimedia %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Article' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('article_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your article %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to be added as protagonist to your article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Article') {
                $entityLink = $this->router->generate('article_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the article %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to add you as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getGroup()) && $this->userAuthentication->isAdminOf($request->getGroup())) {
                $group = $request->getGroup();
                $groupLink = $this->router->generate('group_show', array('slug' => $group->getSlug()));
                return $this->translator->trans('%user% would like to join the group %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$groupLink.'">'.$group.'</a>'));
            } elseif (!is_null($request->getGroup())) {
                $group = $request->getGroup();
                $groupLink = $this->router->generate('group_show', array('slug' => $group->getSlug()));
                return $this->translator->trans('%user% would like you to join the group %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$groupLink.'">'.$group.'</a>'));
            } elseif (!is_null($request->getPage()) && $this->userAuthentication->isAdminOf($request->getPage())) {
                $page = $request->getPage();
                $pageLink = $this->router->generate('page_show', array('slug' => $page->getSlug()));
                return $this->translator->trans('%user% would like to join the page %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$pageLink.'">'.$page.'</a>'));
            } elseif (!is_null($request->getPage())) {
                $page = $request->getPage();
                $pageLink = $this->router->generate('page_show', array('slug' => $page->getSlug()));
                return $this->translator->trans('%user% would like you to join the page %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$pageLink.'">'.$page.'</a>'));
            } elseif ($request->getObject() == 'Relation') {
                return $this->translator->trans('%user% requested you a relation. TODO: relation type!', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>'));
            }
        } elseif ($user->getId() == $request->getFromUser()->getId()) {
            $userLink = $this->router->generate('user_show', array('slug' => $request->getUser()->getSlug()));
            $userBox = $this->getUserBox($request->getUser());
            if (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Event') {
                $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('You requested %user% to be added as protagonist to the event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Disc') {
                $entityLink = $this->router->generate('disc_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('You requested %user% to be added as protagonist to the disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('You requested %user% to be added as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Article') {
                // return __('You requested %user% to be added as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getGroup())) {
                $group = $request->getGroup();
                $groupLink = $this->router->generate('group_show', array('slug' => $group->getSlug()));
                return $this->translator->trans('You requested %user% to join the group %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$groupLink.'">'.$group.'</a>'));
            } elseif (!is_null($request->getPage())) {
                $page = $request->getPage();
                $pageLink = $this->router->generate('page_show', array('slug' => $page->getSlug()));
                return $this->translator->trans('You requested %user% to join the page %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$pageLink.'">'.$page.'</a>'));
            }
        }
    }

    public function getRequestUpdate(Request $request)
    {
        $loading = $this->translator->trans('Loading');
        $accept = $this->translator->trans('Accept');
        $refuse = $this->translator->trans('Refuse');
        switch ($request->getObject()) {
            case 'Relation':
                $acceptPath = $this->router->generate('relation_update', array('choice' => 'accept', 'id' => $request->getId()));
                $refusePath = $this->router->generate('relation_update', array('choice' => 'refuse', 'id' => $request->getId()));
                break;
            default:
                $acceptPath = $this->router->generate('request_update', array('choice' => 'accept', 'id' => $request->getId()));
                $refusePath = $this->router->generate('request_update', array('choice' => 'refuse', 'id' => $request->getId()));
                break;
        }
        return '<a href="'.$acceptPath.'" class="btn btn-primary btn-sm ajax-link" data-loading-text="'.$loading.'">'.$this->getIcon('Ok').' '.$accept.'</a>
                <a href="'.$refusePath.'" class="btn btn-default btn-sm ajax-link" data-loading-text="'.$loading.'">'.$this->getIcon('Remove').' '.$refuse.'</a>';
    }

    public function getNotificationTag(Notification $notification)
    {
        $userLink = $this->router->generate('user_show', array('slug' => $notification->getFromUser()->getSlug()));
        $userBox = $this->getUserBox($notification->getFromUser());
        switch ($this->getClassName($notification->getPost()->getObject()).'_'.$notification->getType()) {
            case 'Event_'.Notification::TYPE_REQUEST_ACCEPTED:
                $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% joined your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
                break;
            case 'Event_'.Notification::TYPE_LIKE:
                $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% likes your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Event_'.Notification::TYPE_COMMENT:
                $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% has commented your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Disc_'.Notification::TYPE_REQUEST_ACCEPTED:
                $entityLink = $this->router->generate('disc_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% joined your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
                break;
            case 'Disc_'.Notification::TYPE_LIKE:
                $entityLink = $this->router->generate('disc_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% likes your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Disc_'.Notification::TYPE_COMMENT:
                $entityLink = $this->router->generate('disc_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% has commented your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'user_like':
                // return __('%user% likes your %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to(__('post'), $this->getPost()->getLinkShow())));
            case 'Group_'.Notification::TYPE_REQUEST_ACCEPTED:
                return $this->translator->trans('%user% joined the group %group%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>'));
                break;
            case 'Fan_'.Notification::TYPE_FAN:
                return $this->translator->trans('%user% became your fan.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>'));
                // return __('%user% became your fan.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow())));
            default:
                return $this->getClassName($notification->getPost()->getObject()).'_'.$notification->getType();
                // return 'Case: '.$notification->getPost()->getObject().'_'.$notification->getType().', PostId: '.$notification->getPostId().', From: '.$notification->getFromUser().', Type: '.$notification->getType().', Object: '.$notification->getObject();
        }
    }

    public function getPostText(Post $post, $relatedObjects = null)
    {
        // $object_page = '@'.$post->getObject().'_index';
        $userLink = $this->router->generate($post->getPublisherType().'_show', array('slug' => $post->getPublisher()->getSlug()));
        $userBox = $this->getUserBox($post->getPublisher());
        switch($this->getClassName($post->getObject()).'_'.$post->getType()) {
            case 'Event_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                $categoryLink  = $this->router->generate('event_category', array('category_slug' => $post->getEntity()->getEntityCategory()->getSlug()));
                return $this->translator->trans('%user% published the event %object% in %category%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>',
                    '%category%' => '<a href="'.$categoryLink.'">'.ucfirst($post->getEntity()->getEntityCategory()->getPlural()).'</a>'
                ));
            case 'Disc_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('disc_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                $categoryLink  = $this->router->generate('disc_category', array('category_slug' => $post->getEntity()->getEntityCategory()->getSlug()));
                return $this->translator->trans('%user% published the dics %object% in %category%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>',
                    '%category%' => '<a href="'.$categoryLink.'">'.ucfirst($post->getEntity()->getEntityCategory()->getPlural()).'</a>'
                ));
            case 'Comment_'.Post::TYPE_CREATION:
                $likeOrComment = 'commented on';
            case 'Like_'.Post::TYPE_CREATION:
                if ($this->getClassName($post->getObject()).'_'.$post->getType() == 'Like_'.Post::TYPE_CREATION) {
                    $likeOrComment = 'likes';
                }
                $publisherLink = $this->router->generate($post->getEntity()->getPost()->getPublisherType().'_show', array('slug' => $post->getEntity()->getPost()->getPublisher()->getSlug()));
                $publisherBox = $this->getUserBox($post->getEntity()->getPost()->getPublisher());
                if (is_null($post->getEntity())) {
                    return 'asd';
                } elseif ($post->getEntity() instanceof Biography) {
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser()->getId() == $post->getPublisherId()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    $objectLink = $this->router->generate($post->getPublisherType().'_biography', array('slug' => $post->getEntity()->getPost()->getPublisher()->getSlug()));
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %biographyLinkStart%Biography%biographyLinkEnd%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%biographyLinkStart%' => '<a href="'.$objectLink.'">', '%biographyLinkEnd%' => '</a>'
                    ));
                } elseif ($post->getEntity() instanceof Event) {
                    $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof Disc) {
                    $objectLink = $this->router->generate('disc_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof ImageAlbum) {
                    $objectLink = $this->router->generate($post->getPublisherType().'_album', array('id' => $post->getEntity()->getId(), 'slug' => $post->getPublisher()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof Multimedia) {
                    $objectLink = $this->router->generate('multimedia_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof Article) {
                    $objectLink = $this->router->generate('article_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getPublisher()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif (!is_null($relatedObjects->getImageId())) {
                    $objectLink = $this->router->generate($post->getPublisherType().'_image', array('id' => $relatedObjects->getImageId(), 'slug' => $post->getPublisher()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %objectLinkStart%a photo%objectLinkEnd%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%objectLinkStart%' => '<a href="'.$objectLink.'">', '%objectLinkEnd%' => '</a>'
                    ));
                }
                break;
            case 'image_album_image_add':
                // return format_number_choice('[1]%user% added a new photo to the album %object%.|(1,+Inf]%user% added %count% new photos to the album %object%.', array(
                //         '%user%'    => link_to($post->getUser(), $post->getUser()->getLinkShow()),
                //         '%count%' => count($post->getObjectIds()),
                //         '%object%'  => link_to($post->getEntity(), $post->getEntity()->getLinkShow())
                //     ), count($post->getObjectIds()));
            case 'Biography_'.Post::TYPE_CREATION:
            case 'Biography_'.Post::TYPE_UPDATE:
                $objectLink = $this->router->generate('user_biography', array('slug' => $post->getUser()->getSlug()));
                return $this->translator->trans('%user% updated '.$post->getPublisherSex('his').' %biographyLinkStart%biography%biographyLinkEnd%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%biographyLinkStart%' => '<a href="'.$objectLink.'">', '%biographyLinkEnd%' => '</a>'
                ));case 'ImageAlbum_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate($post->getPublisherType().'_album', array('id' => $post->getEntity()->getId(), 'slug' => $post->getPublisher()->getSlug()));
                return $this->translator->trans('%user% created the album %object%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                ));
            case 'Image_'.Post::TYPE_CREATION:
            case 'Image_'.Post::TYPE_UPDATE:
                switch ($this->getClassName($post->getEntity())) {
                    case 'Event':
                        $entityLink = $this->router->generate('event_show', array('id' => $post->getEntityId(), 'slug' => $post->getEntity()->getSlug()));
                        $entityString = ' event';
                        break;
                    case 'ImageAlbum':
                        $entityLink = $this->router->generate($post->getPublisherType().'_album', array('id' => $post->getEntityId(), 'slug' => $post->getPublisher()->getSlug()));
                        $albumString = $post->getEntity()->getType() == ImageAlbum::TYPE_ALBUM ? ' album' : '';
                        break;
                    default:
                        $entityLink = '';
                        break;
                }
                return $this->translator->trans('%user% added images to '.$post->getPublisherSex('his').$entityString.' %entity%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%entity%' => '<a href="'.$entityLink.'">'.$post->getEntity().'</a>'
                ));
            case 'Multimedia_'.Post::TYPE_CREATION:
                $multimediaLink = $this->router->generate('multimedia_show', array('id' => $post->getEntityId(), 'slug' => $post->getEntity()->getSlug()));
                return $this->translator->trans('%user% added a new %albumLinkStart%'.$post->getEntity()->typeString().'%albumLinkEnd%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%albumLinkStart%' => '<a href="'.$multimediaLink.'">', '%albumLinkEnd%' => '</a>'
                ));
            case 'Article_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('article_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                return $this->translator->trans('%user% published the article %object%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                ));
            case 'user_'.Post::TYPE_REGISTRATION:
                // return __('%user% registered on Circuito Musica. - '.$post->getPublisherSex('M'), array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow())));
            case 'User_'.Post::TYPE_CREATION:
                return $this->translator->trans('%user% registered on Circuito Musica..', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>'
                ));
            case 'Group_'.Post::TYPE_CREATION:
                $userLink = $this->router->generate('user_show', array('slug' => $post->getGroup()->getCreator()->getSlug()));
                $objectLink = $this->router->generate('group_show', array('slug' => $post->getGroup()->getSlug()));
                return $this->translator->trans('%user% opened the group %group%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getGroup()->getCreator().'</a>',
                    '%group%' => '<a href="'.$objectLink.'">'.$post->getGroup().'</a>'
                ));
            case 'Page_'.Post::TYPE_CREATION:
                $userLink = $this->router->generate('user_show', array('slug' => $post->getPage()->getCreator()->getSlug()));
                $objectLink = $this->router->generate('page_show', array('slug' => $post->getPage()->getSlug()));
                return $this->translator->trans('%user% opened the page %page%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPage()->getCreator().'</a>',
                    '%page%' => '<a href="'.$objectLink.'">'.$post->getPage().'</a>'
                ));
                // return __('%user% has opened the page %page%', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%page%' => link_to($post->getRelatedObject()->getFirst(), $post->getRelatedObject()->getFirst()->getLinkShow())));
            
            case 'user_img_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' profile picture.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow())));
            case 'user_cover_img_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' cover picture.', array('%user%'     => link_to($post->getUser(), $post->getUser()->getLinkShow())));
            case 'Fan_'.Post::TYPE_FAN_USER:
                return 'Fan of an user';
                switch (count($post->getObjectIds())) {
                    default:
                    case 3:
                        $fan3Link = $this->router->generate('user_show', array('slug' => $relatedObjects[2]->getUser()->getSlug()));
                        $fan3Box = $this->getUserBox($relatedObjects[2]->getUser());
                    case 2:
                        $fan2Link = $this->router->generate('user_show', array('slug' => $relatedObjects[1]->getUser()->getSlug()));
                        $fan2Box = $this->getUserBox($relatedObjects[1]->getUser());
                    case 1:
                        $fan1Link = $this->router->generate('user_show', array('slug' => $relatedObjects[0]->getUser()->getSlug()));
                        $fan1Box = $this->getUserBox($relatedObjects[0]->getUser());
                    case 0:
                        break;
                }
                if (count($post->getObjectIds()) == 1) {
                    return $this->translator->trans('%user% became fan of %fan1%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 2) {
                    return $this->translator->trans('%user% became fan of %fan1% and %fan2%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'" '.$fan2Box.'>'.$relatedObjects[1]->getUser().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 3) {
                    return $this->translator->trans('%user% became fan of %fan1%, %fan2% and %fan3%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'" '.$fan2Box.'>'.$relatedObjects[1]->getUser().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'" '.$fan3Box.'>'.$relatedObjects[2]->getUser().'</a>'
                    ));
                } else {
                    $countLink = $this->router->generate('wall__show', array('id' => $pst->getId()));
                    return $this->translator->trans('%user% became fan of %fan1%, %fan2%, %fan3% and %count% more.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'" '.$fan2Box.'>'.$relatedObjects[1]->getUser().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'" '.$fan3Box.'>'.$relatedObjects[2]->getUser().'</a>',
                        '%count%' => '<a href="'.$countLink.'">'.count($post->getObjectIds()).'</a>'
                    ));
                }
            case 'Fan_'.Post::TYPE_FAN_PAGE:
                switch (count($post->getObjectIds())) {
                    default:
                    case 3:
                        $fan3Link = $this->router->generate('page_show', array('slug' => $relatedObjects[2]->getPage()->getSlug()));
                    case 2:
                        $fan2Link = $this->router->generate('page_show', array('slug' => $relatedObjects[1]->getPage()->getSlug()));
                    case 1:
                        $fan1Link = $this->router->generate('page_show', array('slug' => $relatedObjects[0]->getPage()->getSlug()));
                    case 0:
                        break;
                }
                if (count($post->getObjectIds()) == 1) {
                    return $this->translator->trans('%user% became fan of the page %fan1%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 2) {
                    return $this->translator->trans('%user% became fan of the pages %fan1% and %fan2%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getPage().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 3) {
                    return $this->translator->trans('%user% became fan of the pages %fan1%, %fan2% and %fan3%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getPage().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getPage().'</a>'
                    ));
                } else {
                    $countLink = $this->router->generate('wall__show', array('id' => $pst->getId()));
                    return $this->translator->trans('%user% became fan of the pages %fan1%, %fan2%, %fan3% and %count% more.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getPage().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getPage().'</a>',
                        '%count%' => '<a href="'.$countLink.'">'.count($post->getObjectIds()).'</a>'
                    ));
                }
            case 'Fan_'.Post::TYPE_FAN_GROUP:
                switch (count($post->getObjectIds())) {
                    default:
                    case 3:
                        $fan3Link = $this->router->generate('group_show', array('slug' => $relatedObjects[2]->getGroup()->getSlug()));
                    case 2:
                        $fan2Link = $this->router->generate('group_show', array('slug' => $relatedObjects[1]->getGroup()->getSlug()));
                    case 1:
                        $fan1Link = $this->router->generate('group_show', array('slug' => $relatedObjects[0]->getGroup()->getSlug()));
                    case 0:
                        break;
                }
                if (count($post->getObjectIds()) == 1) {
                    return $this->translator->trans('%user% became fan of the group %fan1%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 2) {
                    return $this->translator->trans('%user% became fan of the groups %fan1% and %fan2%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getGroup().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 3) {
                    return $this->translator->trans('%user% became fan of the groups %fan1%, %fan2% and %fan3%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getGroup().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getGroup().'</a>'
                    ));
                } else {
                    $countLink = $this->router->generate('wall__show', array('id' => $pst->getId()));
                    return $this->translator->trans('%user% became fan of the groups %fan1%, %fan2%, %fan3% and %count% more.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getGroup().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getGroup().'</a>',
                        '%count%' => '<a href="'.$countLink.'">'.count($post->getObjectIds()).'</a>'
                    ));
                }
            case 'education_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %studies%.', array('%user%'     => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%studies%' => link_to(__('studies'), '@work_education_user?user='.$post->getUser()->getUsername())));
            case 'education_masterclass_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %masterclasses%.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%masterclasses%' => link_to(__('masterclasses'), '@work_education_user?user='.$post->getUser()->getUsername())));
            case 'job_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %works%.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%works%' => link_to(__('works'), '@work_education_user?user='.$post->getUser()->getUsername())));
            default:
                return $this->getClassName($post->getObject()).'_'.$post->getType();
        }
    }

    public function getEntityPostText(Post $post)
    {
        $userLink = $this->router->generate($post->getPublisherType().'_show', array('slug' => $post->getPublisher()->getSlug()));
        $userBox = $this->getUserBox($post->getPublisher());
        switch($this->getClassName($post->getObject()).'_'.$post->getType()) {
            case 'Comment_'.Post::TYPE_CREATION:
                return '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>';
            // case 'Image_'.Post::TYPE_CREATION:
            //     if (count($post->objectIds()) == 1) {
            //         return $this->translator->trans('%user% added an image.', array(
            //             '%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>'
            //         ));
            //     } else {
            //         return $this->translator->trans('%user% added %count% images.', array(
            //             '%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>',
            //             '%count%' => count($post->objectIds())
            //         ));
            //     }
            default:
                return $this->getClassName($post->getObject()).'_'.$post->getType();
        }
    }

    function getIcon($object)
    {
        switch ($object) {
            case 'Up':
                return '<span class="glyphicon glyphicon-chevron-up"></span>';
            case 'Down':
                return '<span class="glyphicon glyphicon-chevron-down"></span>';
            case 'Prev':
            case 'Back':
                return '<span class="glyphicon glyphicon-chevron-left"></span>';
            case 'Next':
                return '<span class="glyphicon glyphicon-chevron-right"></span>';
            case 'Remove':
                return '<span class="glyphicon glyphicon-remove"></span>';
                break;
            case 'Event':
            case 'Calendar':
            case 'Event_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-calendar"></span>';
            case 'Disc':
            case 'Disc_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-headphones"></span>';
            case 'Article':
            case 'Review':
            case 'Article_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-print"></span>';
            case 'Link':
            case 'Link_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-bookmark"></span>';
            case 'Image':
            case 'Image_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-picture"></span>';
            case 'Multimedia':
            case 'Multimedia_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-film"></span>';
            case 'Page':
            case 'Page_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-list-alt"></span>';
            case 'Group':
            case 'Group_'.Post::TYPE_CREATION:
            case 'Users':
                return '<span class="glyphicons group"></span>';
            case 'Fan':
            case 'Fan_'.Post::TYPE_FAN_USER:
            case 'Fan_'.Post::TYPE_FAN_GROUP:
            case 'Fan_'.Post::TYPE_FAN_PAGE:
                return '<span class="glyphicon glyphicon-flag"></span>';
            case 'User':
                return '<span class="glyphicon glyphicon-user"></span>';
            case 'User_'.Post::TYPE_REGISTRATION:
                return '<span class="glyphicon-user-add"></span>';
            case 'Biography':
            case 'Biography_'.Post::TYPE_UPDATE:
                return '<span class="glyphicon glyphicon-book"></span>';
            case 'Like':
            case 'Like_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-thumbs-up"></span>';
            case 'Comment':
            case 'Comment_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-comment"></span>';
            case 'Comments':
                return '<span class="glyphicons conversation"></span>';
            case 'Wall':
            case 'Post':
                return '<span class="glyphicon glyphicon-th-list"></span>';
            case 'Tag':
                return '<span class="glyphicon glyphicon-tag"></span>';
            case 'Tags':
                return '<span class="glyphicon glyphicon-tags"></span>';
            case 'Work_'.Post::TYPE_EDUCATION:
            case 'Work':
            case 'Job':
            case 'Job_'.Post::TYPE_UPDATE:
                return '<span class="glyphicon glyphicon-briefcase"></span>';
            case 'Education':
            case 'Education_'.Post::TYPE_UPDATE:
                return '<span class="glyphicon glyphicon-book-open"></span>';
            case 'List':
                return '<span class="glyphicon glyphicon-list"></span>';
            case 'Plus':
                return '<span class="glyphicon glyphicon-plus"></span>';
            case 'Minus':
                return '<span class="glyphicon glyphicon-minus"></span>';
            case 'Protagonist':
            case 'Crown':
                return '<span class="glyphicons crown"></span>';
            case 'Ok':
                return '<span class="glyphicon glyphicon-ok"></span>';
            case 'Archive':
                return '<span class="glyphicon glyphicon-folder-close"></span>';
            case 'Time':
                return '<span class="glyphicon glyphicon-time"></span>';
            case 'Map':
                return '<span class="glyphicon glyphicon-map-marker"></span>';
            case 'Edit':
                return '<span class="glyphicon glyphicon-pencil"></span>';
            case 'Alert':
                return '<span class="glyphicon glyphicon-exclamation-sign"></span>';
            case 'Message':
                return '<span class="glyphicon glyphicon-envelope"></span>';
            case 'Globe':
            case 'Notification':
                return '<span class="glyphicon glyphicon-globe"></span>';
            case 'Request':
            case 'Request_in':
                return '<span class="glyphicon glyphicon-bell"></span>';
            case 'Request_out':
                return '<span class="glyphicon glyphicon-share-alt"></span>';
            case 'Relation':
            case 'Relation_'.Post::TYPE_CREATION:
                return '<span class="glyphicons git_branch"></span>';
            case 'Options':
                return '<span class="glyphicons cogwheels"></span>';
            case 'Photo':
                return '<span class="glyphicons camera"></span>';
            case 'Folder':
            case 'Folder_Close':
                return '<span class="glyphicon glyphicon-folder-close"></span>';
            case 'Folder_Open':
                return '<span class="glyphicon glyphicon-folder-open"></span>';
            case 'Info':
                return '<span class="glyphicons pushpin"></span>';
            case 'Fullscreen':
                return '<span class="glyphicon glyphicon-fullscreen"></span>';
            case 'Login':
                return '<span class="glyphicons lock"></span>';
            case 'Sponsored':
                return '<span class="glyphicon glyphicon-bullhorn"></span>';
            case 'Vip':
                return '<span class="glyphicon glyphicon-fire"></span>';
            default:
                return '<span style="color:red;">missing glyphicon for '.$object.'</span>';
        }
    }

    public function getTooltip($what, $options = array())
    {
        if (empty($what) || is_null($what)) {
            return '';
        }

        $options = array_merge(array(
            'placement' => 'top auto',
            'container' => 'body',
            'selector' => null,
            'html' => true,
            'separator' => '<br/>',
            'closure' => null,
            'args' => array(),
            'limit' => 20
        ), $options);

        if (is_array($what) && !is_null($options['limit'])) {
            $what = array_slice($what, 0, $options['limit'], true);
        }

        if (is_array($what) && !is_null($options['closure'])) {
            $closure = create_function('$v, $a', 'return '.$options['closure'].';');
            foreach ($what as &$v) {
                $v = $closure($v, $options[args]);
            }
        }

        if (is_array($what)) {
            $what = join($what, $options['separator']);
        }

        return 'data-toggle="tooltip" data-placement="'.$options['placement'].'" data-container="'.$options['container'].'" data-html="'.($options['html'] ? 'true' : 'false').'" data-title="'.$what.'"';
    }

    public function getModal($options = array())
    {
        $options = array_merge(array(
            'title' => 'false',
            'text' => null,
            'btn1' => null,
            'btn2' => null,
            'btn1Class' => null,
            'btn2Class' => null,
        ), $options);

        $tag = 'confirm';
        foreach ($options as $attr => $value) {
            if (!is_null($value)) {
                $tag .=' data-confirm-'.$attr.'="'.$value.'"';
            }
        }

        return $tag;
    }

    public function getVimeoImage($id, $dim = 'medium')
    {
        $hash = unserialize(file_get_contents('https://vimeo.com/api/v2/video/'.$id.'.php'));

        return $hash[0]['thumbnail_'.$dim];
    }

    public function getSoundcloudImage($id)
    {
        $json = json_decode(file_get_contents('https://api.soundcloud.com/tracks/'.$id.'.json?client_id=69181ee06df52a18c656847d8796d1c0'));

        if (!is_null($json->artwork_url)) {
            return preg_replace('/-[\w\d]+\.jpg/', '-t300x300.jpg', $json->artwork_url);
        } else {
            return $json->waveform_url;
        }
    }

    public function getName()
    {
        return 'cm_extension';
    }
}


/*
<?php

namespace CM\CMBundle\Twig;

use Symfony\Component\Intl\Intl;
use Symfony\Component\Translation\Translator;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use CM\CMBundle\Service\Helper;
use Symfony\Component\Security\Core\SecurityContext;
use CM\CMBundle\Service\UserAuthentication;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\Disc;
use CM\CMBundle\Entity\ImageAlbum;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\EntityUser;
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

    private $imagineFilter;

    private $options;
    
    private $request;
    
    private $environment;

    private $controllerName;

    private $actionName;


    public function __construct(
        Translator $translator,
        Router $router,
        RequestStack $requestStack,
        Helper $helper,
        SecurityContext $securityContext,
        UserAuthentication $userAuthentication,
        CacheManager $imagineFilter,
        $options = array()
    )
    {
        $this->translator = $translator;
        $this->router = $router;
        $this->request = $requestStack->getCurrentRequest();
        $this->helper = $helper;
        $this->securityContext = $securityContext;
        $this->userAuthentication = $userAuthentication;
        $this->imagineFilter = $imagineFilter;
        $this->options = array_merge(array(
            'images_abs_dir' => '/',
            'sizes' => array()
        ), $options);
    }
    
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    public function getFilters()
    {
        return array(
            'ceil' => new \Twig_Filter_Method($this, 'ceil'),
            'class_name' => new \Twig_Filter_Method($this, 'getClassName'),
            'simple_format_text' => new \Twig_Filter_Method($this, 'getSimpleFormatText'),
            'show_text' => new \Twig_Filter_Method($this, 'getShowText'),
            'default_img' => new \Twig_Filter_Method($this, 'getDefaultImg'),
        );
    }

    public function getFunctions()
    {
        return array(
            'controller_name' => new \Twig_Function_Method($this, 'getControllerName'),
            'action_name' => new \Twig_Function_Method($this, 'getActionName'),
            'datetime_format' => new \Twig_Function_Method($this, 'getDateTimeFormat', array('is_safe' => array('html'))),
            'can_manage' => new \Twig_Function_Method($this, 'getCanManage'),
            'is_admin' => new \Twig_Function_Method($this, 'getIsAdmin'),
            'related_object' => new \Twig_Function_Method($this, 'getRelatedObject'),
            'delete_link' => new \Twig_Function_Method($this, 'getDeleteLink', array('is_safe' => array('html'))),
            'entity_short_text' => new \Twig_Function_Method($this, 'getEntityShortText', array('is_safe' => array('html'))),
            'user_box' => new \Twig_Function_Method($this, 'getUserBox', array('is_safe' => array('html'))),
            'show_img_box' => new \Twig_Function_Method($this, 'getShowImgBox', array('is_safe' => array('html'))),
            'request_tag' => new \Twig_Function_Method($this, 'getRequestTag', array('is_safe' => array('html'))),
            'request_update' => new \Twig_Function_Method($this, 'getRequestUpdate', array('is_safe' => array('html'))),
            'notification_tag' => new \Twig_Function_Method($this, 'getNotificationTag', array('is_safe' => array('html'))),
            'post_text' => new \Twig_Function_Method($this, 'getPostText', array('is_safe' => array('html'))),
            'entity_post_text' => new \Twig_Function_Method($this, 'getEntityPostText', array('is_safe' => array('html'))),
            'icon' => new \Twig_Function_Method($this, 'getIcon', array('is_safe' => array('html'))),
            'tooltip' => new \Twig_Function_Method($this, 'getTooltip', array('is_safe' => array('html'))),
            'modal' => new \Twig_Function_Method($this, 'getModal', array('is_safe' => array('html'))),
            'vimeoImage' => new \Twig_Function_Method($this, 'getVimeoImage', array('is_safe' => array('html'))),
            'soundcloudImage' => new \Twig_Function_Method($this, 'getSoundcloudImage', array('is_safe' => array('html'))),
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
        $css = (isset($options['class'])) ? ' class="'.$options['class'].'"' : '';

        $text = Helper::pregtr($text, array(
            "/(\r\n|\r)/" => "\n",            // lets make them newlines crossplatform
            "/\n{2,}/"    => "</p><p$css>" // turn two and more newlines into paragraph
        ));

        // turn single newline into <br/>
        $text = str_replace("\n", "\n<br />", $text);
        return '<p'.$css.'>'.$text.'</p>'; // wrap the first and last line in paragraphs before we're done
    }

    function getShowText($text)
    {
        $actual_length = strlen($text);
        $stripped_length = strlen(strip_tags($text));

        if ($actual_length != $stripped_length) {
            return $text;
        } else {
            return $this->getSimpleFormatText($text);
        }
    }

    function getDefaultImg($image, $options = array())
    {
        $options = array_merge(array(
            'default' => 'default.png',
            'path' => ''
        ), $options);

        if (is_null($image) || empty($image)) {
            $image = $options['default'];
        }

        return $options['path'].$image;
    }

    public function getControllerName()
    {
        if (is_null($this->controllerName)) {
            $pattern = "/Controller\\\([a-zA-Z]*)Controller/";
            $matches = array();
            preg_match($pattern, $this->request->get('_controller'), $matches);
            
            $this->controllerName = strtolower($matches[1]);
        }

        return $this->controllerName;
    }

    public function getActionName()
    {
        if (is_null($this->actionName)) {
            $pattern = "/::([a-zA-Z]*)Action/";
            $matches = array();
            preg_match($pattern, $this->request->get('_controller'), $matches);
            
            $this->actionName = strtolower($matches[1]);
        }

        return $this->actionName;
    }

    public function getDateTimeFormat($lang = 'js')
    {
        return $this->helper->dateTimeFormat($lang);
    }

    public function getCanManage($object)
    {
        return $this->userAuthentication->canManage($object);
    }

    public function getIsAdmin($object)
    {
        return $this->userAuthentication->isAdminOf($object);
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
            $text = '<span class="glyphicon glyphicon-'.$options['icon'].'"></span> '.$text;
        }
        unset($options['text'], $options['icon']);
        if (is_null($options['class'])) {
            unset($options['class']);
        }

        return '<a href="'.$link.'" >'.$text.'</a>';

        // echo link_to($text, $link, $options);
    }

    public function getEntityShortText(Entity $entity, $options = array())
    {
        $options = array_merge(array(
            'max' => 400,
            'stripped' => false,
            'more' => false,
            'moreText' => 'show more',
            'moreTextAlt' => 'show less'
        ), $options);

        if (!$options['more'] && $entity->getExtract() && ($this->securityContext->isGranted('ROLE_ADMIN') || $this->securityContext->isGranted('ROLE_CLIENT'))) {
            $text = $entity->getExtract();
        } else {
            $text = $entity->getText();
        }

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
                $text = rtrim($matches[0], '.:').'.';
            } else {
                $text = rtrim(Helper::truncate_text($text_stripped, $max, '', true), ',.;!?:');
                if (!$options['more']) {
                    $text .= '...';
                }
            }
        }

        $text = $stripped ? $this->getSimpleFormatText($text) : $this->getShowText($text);

        if ($options['more']) {
            $text .= ' <div id="show_more-entity_'.$entity->getId().'">'.substr($entity->getText(), strlen($text)).'</div>';
            // $text .= '<a href="show_more-entity_'.$entity->getId().'" class="box-collapser collapsed" data-toggle="collapse"'.' text-alt="'+ $this->translator->trans($options['moreTextAlt']).'">'.$this->translator->trans($options['moreText']).'</a>';
        }

        return $text;
    }

    public function getUserBox($publisher, $options = array())
    {
        if (!$publisher instanceof User) {
            return '';
        }

        $options = array_merge(array(
        ), $options);

        // var_dump($this->twig->getFilter('e')());die;

        // $img = $this->imagineFilter->getBrowserPath($publisher->getImg(), 50, false);
        // $imgBox = $this->getShowImgBox($img, array('width' => 35, 'height' => 35, 'offset' => $publisher->getImgOffset(), 'box_attributes' => array('class' => 'pull-left')));

        return 'user-popover data-title="'.$publisher.'" data-href="'.$this->router->generate('user_popover', array('slug' => $publisher->getSlug())).'"';
        // $this->getShowImgBox($img, array('width' => 35, 'height' => 35, 'offset' => $publisher->getImgOffset(), 'box_attributes' => array('class' => 'pull-left')))
    }

    public function getShowImgBox($img, $options = array())
    {
        $options = array_merge(array(
            'width' => 150,
            'height' => null,
            'ratio' => null,
            'offset' => null,
            'default' => false,
            'link' => null,
            'box_attributes' => array(),
            'img_attributes' => array(),
            'user_box' => null
        ), $options);

        $width  = $options['width'];
        $height = is_null($options['height']) ? $width : $options['height'];

        // Get image dimensions
        $fileName = preg_replace('/^\/.*\//', '', $img);
        $imageFileSize = @getimagesize($this->options['images_dir'].'/'.$fileName);
        if (!$imageFileSize) {
            return '';
        }

        // Ratio
        $imageRatio = $imageFileSize[0] / $imageFileSize[1];
        if (!is_null($options['ratio'])) {
            $boxRatio = $options['ratio'];
        } else {
            $boxRatio = $width / $height;
        }
        $ratio = $imageRatio / $boxRatio;

        // Image format
        if ($ratio >= 1) {
            $imageResizedHeight = 100;
        } else {
            $imageResizedHeight = $boxRatio / $imageRatio * 100;
        }

        // Offset
        $imgStyle = array();
        // if (!is_null($options['offset'])) {
        //     $imgStyle[] = 'margin-'.($ratio > 1 ? 'left' : 'top').': -'.$options['offset'].'%';
        // } elseif (is_null($options['ratio'])) {
        //     if ($ratio > 1) { // landscape
        //         $imgStyle[] = 'left: -'.(($imageRatio / $boxRatio - 1) / 2 * 100).'%';
        //     } elseif ($ratio < 1) { // portrait
        //         $imgStyle[] = 'top: -'.(min($imageResizedHeight - $height, $imageResizedHeight / 10) / $height * 100).'%';
        //     }
        // }

        // Attributes
        if (!is_null($options['ratio'])) {
            $options['box_attributes']['style'] = 'width: 100%;  padding-top: '.(1 / $boxRatio * 100).'%;'.(array_key_exists('style', $options['box_attributes']) ? ' '.$options['box_attributes']['style'] : '');
        } else {
            $options['box_attributes']['style'] = 'width: '.$width.'px;  height: '.$height.'px;'.(array_key_exists('style', $options['box_attributes']) ? ' '.$options['box_attributes']['style'] : '');
        }
        $options['box_attributes']['class'] = 'image_box'.(array_key_exists('class', $options['box_attributes']) ? ' '.$options['box_attributes']['class'] : '');

        if ($ratio == 1) {
            $imgStyle[] = 'height: 100%';
        } elseif (($ratio > 1 && is_null($options['ratio'])) || ($ratio < 1 && !is_null($options['ratio']))) {
            $imgStyle[] = 'height: 100%';
        } else {
            $imgStyle[] = 'width: 100%';
        }
        // $imgStyle[] = 'height: '.$imageResizedHeight.'%';
        if (array_key_exists('style', $options['img_attributes'])) {
            $imgStyle[] = $options['img_attributes']['style'];
            unset($options['img_attributes']['style']);
        }

        // Write img tag
        $imgTag = '<img ratio="'.$boxRatio.'" src="'.$img.'" style="';
        foreach ($imgStyle as $attr) {
            $imgTag .= $attr.'; ';
        }
        $imgTag .= '"';
        foreach ($options['img_attributes'] as $key => $attr) {
            $imgTag .= ' '.$key.'="'.$attr.'"';
        }
        $imgTag .= ' />';

        // Write box tag
        if (!is_null($options['link'])) {
            $boxTag = '<a href="'.$options['link'].'"';
            $boxTagEnd = '</a>';
        } else {
            $boxTag = '<div';
            $boxTagEnd = '</div>';
        }
        foreach ($options['box_attributes'] as $key => $attr) {
            $boxTag .= ' '.$key.'="'.$attr.'"';
        }

        if (!is_null($options['user_box'])) {
            $boxTag = '<div '.$this->getUserBox($options['user_box']).'>'.$boxTag;
            $boxTagEnd = '</div>'.$boxTagEnd;
        }
        
        return $boxTag.'>'.$imgTag.$boxTagEnd;
    }

    public function getRequestTag(Request $request)
    {
        $user = $this->securityContext->getToken()->getUser();

        if ($user->getId() == $request->getUser()->getId()) {
            $userLink = $this->router->generate('user_show', array('slug' => $request->getFromUser()->getSlug()));
            $userBox = $this->getUserBox($request->getFromUser());
            if (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Event' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Event') {
                $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Disc' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('disc_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to be added as protagonist to your disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Disc') {
                $entityLink = $this->router->generate('disc_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to add you as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Multimedia' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('multimedia_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your multimedia %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Multimedia') {
                $entityLink = $this->router->generate('multimedia_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the multimedia %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Article' && $request->getEntity()->getEntityUsers()[$user->getId()]->getStatus() == EntityUser::STATUS_REQUESTED) {
                $entityLink = $this->router->generate('article_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to be added as protagonist to your article %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to be added as protagonist to your article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Article') {
                $entityLink = $this->router->generate('article_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('%user% would like to add you as protagonist to the article %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('%user% would like to add you as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getGroup()) && $this->userAuthentication->isAdminOf($request->getGroup())) {
                $group = $request->getGroup();
                $groupLink = $this->router->generate('group_show', array('slug' => $group->getSlug()));
                return $this->translator->trans('%user% would like to join the group %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$groupLink.'">'.$group.'</a>'));
            } elseif (!is_null($request->getGroup())) {
                $group = $request->getGroup();
                $groupLink = $this->router->generate('group_show', array('slug' => $group->getSlug()));
                return $this->translator->trans('%user% would like you to join the group %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$groupLink.'">'.$group.'</a>'));
            } elseif (!is_null($request->getPage()) && $this->userAuthentication->isAdminOf($request->getPage())) {
                $page = $request->getPage();
                $pageLink = $this->router->generate('page_show', array('slug' => $page->getSlug()));
                return $this->translator->trans('%user% would like to join the page %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$pageLink.'">'.$page.'</a>'));
            } elseif (!is_null($request->getPage())) {
                $page = $request->getPage();
                $pageLink = $this->router->generate('page_show', array('slug' => $page->getSlug()));
                return $this->translator->trans('%user% would like you to join the page %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>', '%object%' => '<a href="'.$pageLink.'">'.$page.'</a>'));
            } elseif ($request->getObject() == 'Relation') {
                return $this->translator->trans('%user% requested you a relation. TODO: relation type!', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getFromUser().'</a>'));
            }
        } elseif ($user->getId() == $request->getFromUser()->getId()) {
            $userLink = $this->router->generate('user_show', array('slug' => $request->getUser()->getSlug()));
            $userBox = $this->getUserBox($request->getUser());
            if (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Event') {
                $entityLink = $this->router->generate('event_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('You requested %user% to be added as protagonist to the event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Disc') {
                $entityLink = $this->router->generate('disc_show', array('id' => $request->getEntity()->getId(), 'slug' => $request->getEntity()->getSlug()));
                return $this->translator->trans('You requested %user% to be added as protagonist to the disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$request->getEntity()->getTitle().'</a>'));
                // return __('You requested %user% to be added as protagonist to the disc %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getEntity()) && $this->getClassName($request->getEntity()) == 'Article') {
                // return __('You requested %user% to be added as protagonist to the article %object%.', array('%user%' => link_to($this->getUserRelatedByUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to($this->getEntity(), $this->getEntity()->getLinkShow())));
            } elseif (!is_null($request->getGroup())) {
                $group = $request->getGroup();
                $groupLink = $this->router->generate('group_show', array('slug' => $group->getSlug()));
                return $this->translator->trans('You requested %user% to join the group %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$groupLink.'">'.$group.'</a>'));
            } elseif (!is_null($request->getPage())) {
                $page = $request->getPage();
                $pageLink = $this->router->generate('page_show', array('slug' => $page->getSlug()));
                return $this->translator->trans('You requested %user% to join the page %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$request->getUser().'</a>', '%object%' => '<a href="'.$pageLink.'">'.$page.'</a>'));
            }
        }
    }

    public function getRequestUpdate(Request $request)
    {
        $loading = $this->translator->trans('Loading');
        $accept = $this->translator->trans('Accept');
        $refuse = $this->translator->trans('Refuse');
        switch ($request->getObject()) {
            case 'Relation':
                $acceptPath = $this->router->generate('relation_update', array('choice' => 'accept', 'id' => $request->getId()));
                $refusePath = $this->router->generate('relation_update', array('choice' => 'refuse', 'id' => $request->getId()));
                break;
            default:
                $acceptPath = $this->router->generate('request_update', array('choice' => 'accept', 'id' => $request->getId()));
                $refusePath = $this->router->generate('request_update', array('choice' => 'refuse', 'id' => $request->getId()));
                break;
        }
        return '<a href="'.$acceptPath.'" class="btn btn-primary btn-sm ajax-link" data-loading-text="'.$loading.'">'.$this->getIcon('Ok').' '.$accept.'</a>
                <a href="'.$refusePath.'" class="btn btn-default btn-sm ajax-link" data-loading-text="'.$loading.'">'.$this->getIcon('Remove').' '.$refuse.'</a>';
    }

    public function getNotificationTag(Notification $notification)
    {
        $userLink = $this->router->generate('user_show', array('slug' => $notification->getFromUser()->getSlug()));
        $userBox = $this->getUserBox($notification->getFromUser());
        switch ($this->getClassName($notification->getPost()->getObject()).'_'.$notification->getType()) {
            case 'Event_'.Notification::TYPE_REQUEST_ACCEPTED:
                $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% joined your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
                break;
            case 'Event_'.Notification::TYPE_LIKE:
                $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% likes your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Event_'.Notification::TYPE_COMMENT:
                $entityLink = $this->router->generate('event_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% has commented your event %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Disc_'.Notification::TYPE_REQUEST_ACCEPTED:
                $entityLink = $this->router->generate('disc_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% joined your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
                break;
            case 'Disc_'.Notification::TYPE_LIKE:
                $entityLink = $this->router->generate('disc_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% likes your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'Disc_'.Notification::TYPE_COMMENT:
                $entityLink = $this->router->generate('disc_show', array('id' => $notification->getPost()->getEntity()->getId(), 'slug' => $notification->getPost()->getEntity()->getSlug()));
                return $this->translator->trans('%user% has commented your disc %object%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>', '%object%' => '<a href="'.$entityLink.'">'.$notification->getPost()->getEntity().'</a>'));
            case 'user_like':
                // return __('%user% likes your %object%.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow()), '%object%' => link_to(__('post'), $this->getPost()->getLinkShow())));
            case 'Group_'.Notification::TYPE_REQUEST_ACCEPTED:
                return $this->translator->trans('%user% joined the group %group%.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>'));
                break;
            case 'Fan_'.Notification::TYPE_FAN:
                return $this->translator->trans('%user% became your fan.', array('%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$notification->getFromUser().'</a>'));
                // return __('%user% became your fan.', array('%user%' => link_to($this->getUserRelatedByFromUserId(), $this->getUserRelatedByFromUserId()->getLinkShow())));
            default:
                return $this->getClassName($notification->getPost()->getObject()).'_'.$notification->getType();
                // return 'Case: '.$notification->getPost()->getObject().'_'.$notification->getType().', PostId: '.$notification->getPostId().', From: '.$notification->getFromUser().', Type: '.$notification->getType().', Object: '.$notification->getObject();
        }
    }

    public function getPostText(Post $post, $relatedObjects = null)
    {
        // $object_page = '@'.$post->getObject().'_index';
        $userLink = $this->router->generate($post->getPublisherType().'_show', array('slug' => $post->getPublisher()->getSlug()));
        $userBox = $this->getUserBox($post->getPublisher());
        switch($this->getClassName($post->getObject()).'_'.$post->getType()) {
            case 'Event_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                $categoryLink  = $this->router->generate('event_category', array('category_slug' => $post->getEntity()->getEntityCategory()->getSlug()));
                return $this->translator->trans('%user% published the event %object% in %category%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>',
                    '%category%' => '<a href="'.$categoryLink.'">'.ucfirst($post->getEntity()->getEntityCategory()->getPlural()).'</a>'
                ));
            case 'Disc_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('disc_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                $categoryLink  = $this->router->generate('disc_category', array('category_slug' => $post->getEntity()->getEntityCategory()->getSlug()));
                return $this->translator->trans('%user% published the dics %object% in %category%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>',
                    '%category%' => '<a href="'.$categoryLink.'">'.ucfirst($post->getEntity()->getEntityCategory()->getPlural()).'</a>'
                ));
            case 'Comment_'.Post::TYPE_CREATION:
                $likeOrComment = 'commented on';
            case 'Like_'.Post::TYPE_CREATION:
                if ($this->getClassName($post->getObject()).'_'.$post->getType() == 'Like_'.Post::TYPE_CREATION) {
                    $likeOrComment = 'likes';
                }
                $publisherLink = $this->router->generate($post->getEntity()->getPost()->getPublisherType().'_show', array('slug' => $post->getEntity()->getPost()->getPublisher()->getSlug()));
                $publisherBox = $this->getUserBox($post->getEntity()->getPost()->getPublisher());
                if (is_null($post->getEntity())) {
                    return 'asd';
                } elseif ($post->getEntity() instanceof Biography) {
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser()->getId() == $post->getPublisherId()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    $objectLink = $this->router->generate($post->getPublisherType().'_biography', array('slug' => $post->getEntity()->getPost()->getPublisher()->getSlug()));
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %biographyLinkStart%Biography%biographyLinkEnd%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%biographyLinkStart%' => '<a href="'.$objectLink.'">', '%biographyLinkEnd%' => '</a>'
                    ));
                } elseif ($post->getEntity() instanceof Event) {
                    $objectLink = $this->router->generate('event_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof Disc) {
                    $objectLink = $this->router->generate('disc_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof ImageAlbum) {
                    $objectLink = $this->router->generate($post->getPublisherType().'_album', array('id' => $post->getEntity()->getId(), 'slug' => $post->getPublisher()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof Multimedia) {
                    $objectLink = $this->router->generate('multimedia_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif ($post->getEntity() instanceof Article) {
                    $objectLink = $this->router->generate('article_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getPublisher()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %publisher% %object%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%publisher%' => $publisher,
                        '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                    ));
                } elseif (!is_null($relatedObjects->getImageId())) {
                    $objectLink = $this->router->generate($post->getPublisherType().'_image', array('id' => $relatedObjects->getImageId(), 'slug' => $post->getPublisher()->getSlug()));
                    if ($this->securityContext->isGranted('ROLE_USER') && $this->securityContext->getToken()->getUser() == $post->getPublisher()) {
                        $publisher = $this->translator->trans($post->getPublisherSex('his'));
                    } else {
                        $publisher = $this->translator->trans('%publisher%\'s', array('%publisher%' => '<a href="'.$publisherLink.'" '.$publisherBox.'>'.$post->getEntity()->getPost()->getPublisher().'</a>'));
                    }
                    return $this->translator->trans('%user% '.$likeOrComment.' %objectLinkStart%a photo%objectLinkEnd%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%objectLinkStart%' => '<a href="'.$objectLink.'">', '%objectLinkEnd%' => '</a>'
                    ));
                }
                break;
            case 'image_album_image_add':
                // return format_number_choice('[1]%user% added a new photo to the album %object%.|(1,+Inf]%user% added %count% new photos to the album %object%.', array(
                //         '%user%'    => link_to($post->getUser(), $post->getUser()->getLinkShow()),
                //         '%count%' => count($post->getObjectIds()),
                //         '%object%'  => link_to($post->getEntity(), $post->getEntity()->getLinkShow())
                //     ), count($post->getObjectIds()));
            case 'Biography_'.Post::TYPE_CREATION:
            case 'Biography_'.Post::TYPE_UPDATE:
                $objectLink = $this->router->generate('user_biography', array('slug' => $post->getUser()->getSlug()));
                return $this->translator->trans('%user% updated '.$post->getPublisherSex('his').' %biographyLinkStart%biography%biographyLinkEnd%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%biographyLinkStart%' => '<a href="'.$objectLink.'">', '%biographyLinkEnd%' => '</a>'
                ));case 'ImageAlbum_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate($post->getPublisherType().'_album', array('id' => $post->getEntity()->getId(), 'slug' => $post->getPublisher()->getSlug()));
                return $this->translator->trans('%user% created the album %object%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                ));
            case 'Image_'.Post::TYPE_CREATION:
            case 'Image_'.Post::TYPE_UPDATE:
                switch ($this->getClassName($post->getEntity())) {
                    case 'Event':
                        $entityLink = $this->router->generate('event_show', array('id' => $post->getEntityId(), 'slug' => $post->getEntity()->getSlug()));
                        $entityString = ' event';
                        break;
                    case 'ImageAlbum':
                        $entityLink = $this->router->generate($post->getPublisherType().'_album', array('id' => $post->getEntityId(), 'slug' => $post->getPublisher()->getSlug()));
                        $albumString = $post->getEntity()->getType() == ImageAlbum::TYPE_ALBUM ? ' album' : '';
                        break;
                    default:
                        $entityLink = '';
                        break;
                }
                return $this->translator->trans('%user% added images to '.$post->getPublisherSex('his').$entityString.' %entity%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%entity%' => '<a href="'.$entityLink.'">'.$post->getEntity().'</a>'
                ));
            case 'Multimedia_'.Post::TYPE_CREATION:
                $multimediaLink = $this->router->generate('multimedia_show', array('id' => $post->getEntityId(), 'slug' => $post->getEntity()->getSlug()));
                return $this->translator->trans('%user% added a new %albumLinkStart%'.$post->getEntity()->typeString().'%albumLinkEnd%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%albumLinkStart%' => '<a href="'.$multimediaLink.'">', '%albumLinkEnd%' => '</a>'
                ));
            case 'Article_'.Post::TYPE_CREATION:
                $objectLink = $this->router->generate('article_show', array('id' => $post->getEntity()->getId(), 'slug' => $post->getEntity()->getSlug()));
                return $this->translator->trans('%user% published the article %object%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                    '%object%' => '<a href="'.$objectLink.'">'.$post->getEntity().'</a>'
                ));
            case 'user_'.Post::TYPE_REGISTRATION:
                // return __('%user% registered on Circuito Musica. - '.$post->getPublisherSex('M'), array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow())));
            case 'User_'.Post::TYPE_CREATION:
                return $this->translator->trans('%user% registered on Circuito Musica..', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>'
                ));
            case 'Group_'.Post::TYPE_CREATION:
                $userLink = $this->router->generate('user_show', array('slug' => $post->getGroup()->getCreator()->getSlug()));
                $objectLink = $this->router->generate('group_show', array('slug' => $post->getGroup()->getSlug()));
                return $this->translator->trans('%user% opened the group %group%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getGroup()->getCreator().'</a>',
                    '%group%' => '<a href="'.$objectLink.'">'.$post->getGroup().'</a>'
                ));
            case 'Page_'.Post::TYPE_CREATION:
                $userLink = $this->router->generate('user_show', array('slug' => $post->getPage()->getCreator()->getSlug()));
                $objectLink = $this->router->generate('page_show', array('slug' => $post->getPage()->getSlug()));
                return $this->translator->trans('%user% opened the page %page%.', array(
                    '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPage()->getCreator().'</a>',
                    '%page%' => '<a href="'.$objectLink.'">'.$post->getPage().'</a>'
                ));
                // return __('%user% has opened the page %page%', array('%user%' => link_to($post->getPublisher(), $post->getPublisher()->getLinkShow()), '%page%' => link_to($post->getRelatedObject()->getFirst(), $post->getRelatedObject()->getFirst()->getLinkShow())));
            
            case 'user_img_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' profile picture.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow())));
            case 'user_cover_img_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' cover picture.', array('%user%'     => link_to($post->getUser(), $post->getUser()->getLinkShow())));
            case 'Fan_'.Post::TYPE_FAN_USER:
                return 'Fan of an user';
                switch (count($post->getObjectIds())) {
                    default:
                    case 3:
                        $fan3Link = $this->router->generate('user_show', array('slug' => $relatedObjects[2]->getUser()->getSlug()));
                        $fan3Box = $this->getUserBox($relatedObjects[2]->getUser());
                    case 2:
                        $fan2Link = $this->router->generate('user_show', array('slug' => $relatedObjects[1]->getUser()->getSlug()));
                        $fan2Box = $this->getUserBox($relatedObjects[1]->getUser());
                    case 1:
                        $fan1Link = $this->router->generate('user_show', array('slug' => $relatedObjects[0]->getUser()->getSlug()));
                        $fan1Box = $this->getUserBox($relatedObjects[0]->getUser());
                    case 0:
                        break;
                }
                if (count($post->getObjectIds()) == 1) {
                    return $this->translator->trans('%user% became fan of %fan1%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 2) {
                    return $this->translator->trans('%user% became fan of %fan1% and %fan2%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'" '.$fan2Box.'>'.$relatedObjects[1]->getUser().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 3) {
                    return $this->translator->trans('%user% became fan of %fan1%, %fan2% and %fan3%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'" '.$fan2Box.'>'.$relatedObjects[1]->getUser().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'" '.$fan3Box.'>'.$relatedObjects[2]->getUser().'</a>'
                    ));
                } else {
                    $countLink = $this->router->generate('wall__show', array('id' => $pst->getId()));
                    return $this->translator->trans('%user% became fan of %fan1%, %fan2%, %fan3% and %count% more.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'" '.$fan1Box.'>'.$relatedObjects[0]->getUser().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'" '.$fan2Box.'>'.$relatedObjects[1]->getUser().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'" '.$fan3Box.'>'.$relatedObjects[2]->getUser().'</a>',
                        '%count%' => '<a href="'.$countLink.'">'.count($post->getObjectIds()).'</a>'
                    ));
                }
            case 'Fan_'.Post::TYPE_FAN_PAGE:
                switch (count($post->getObjectIds())) {
                    default:
                    case 3:
                        $fan3Link = $this->router->generate('page_show', array('slug' => $relatedObjects[2]->getPage()->getSlug()));
                    case 2:
                        $fan2Link = $this->router->generate('page_show', array('slug' => $relatedObjects[1]->getPage()->getSlug()));
                    case 1:
                        $fan1Link = $this->router->generate('page_show', array('slug' => $relatedObjects[0]->getPage()->getSlug()));
                    case 0:
                        break;
                }
                if (count($post->getObjectIds()) == 1) {
                    return $this->translator->trans('%user% became fan of the page %fan1%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 2) {
                    return $this->translator->trans('%user% became fan of the pages %fan1% and %fan2%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getPage().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 3) {
                    return $this->translator->trans('%user% became fan of the pages %fan1%, %fan2% and %fan3%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getPage().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getPage().'</a>'
                    ));
                } else {
                    $countLink = $this->router->generate('wall__show', array('id' => $pst->getId()));
                    return $this->translator->trans('%user% became fan of the pages %fan1%, %fan2%, %fan3% and %count% more.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getPage().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getPage().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getPage().'</a>',
                        '%count%' => '<a href="'.$countLink.'">'.count($post->getObjectIds()).'</a>'
                    ));
                }
            case 'Fan_'.Post::TYPE_FAN_GROUP:
                switch (count($post->getObjectIds())) {
                    default:
                    case 3:
                        $fan3Link = $this->router->generate('group_show', array('slug' => $relatedObjects[2]->getGroup()->getSlug()));
                    case 2:
                        $fan2Link = $this->router->generate('group_show', array('slug' => $relatedObjects[1]->getGroup()->getSlug()));
                    case 1:
                        $fan1Link = $this->router->generate('group_show', array('slug' => $relatedObjects[0]->getGroup()->getSlug()));
                    case 0:
                        break;
                }
                if (count($post->getObjectIds()) == 1) {
                    return $this->translator->trans('%user% became fan of the group %fan1%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 2) {
                    return $this->translator->trans('%user% became fan of the groups %fan1% and %fan2%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getGroup().'</a>'
                    ));
                } elseif (count($post->getObjectIds()) == 3) {
                    return $this->translator->trans('%user% became fan of the groups %fan1%, %fan2% and %fan3%.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getGroup().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getGroup().'</a>'
                    ));
                } else {
                    $countLink = $this->router->generate('wall__show', array('id' => $pst->getId()));
                    return $this->translator->trans('%user% became fan of the groups %fan1%, %fan2%, %fan3% and %count% more.', array(
                        '%user%' => '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>',
                        '%fan1%' => '<a href="'.$fan1Link.'">'.$relatedObjects[0]->getGroup().'</a>',
                        '%fan2%' => '<a href="'.$fan2Link.'">'.$relatedObjects[1]->getGroup().'</a>',
                        '%fan3%' => '<a href="'.$fan3Link.'">'.$relatedObjects[2]->getGroup().'</a>',
                        '%count%' => '<a href="'.$countLink.'">'.count($post->getObjectIds()).'</a>'
                    ));
                }
            case 'education_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %studies%.', array('%user%'     => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%studies%' => link_to(__('studies'), '@work_education_user?user='.$post->getUser()->getUsername())));
            case 'education_masterclass_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %masterclasses%.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%masterclasses%' => link_to(__('masterclasses'), '@work_education_user?user='.$post->getUser()->getUsername())));
            case 'job_update':
                // return __('%user% has updated '.$post->getPublisherSex('his').' %works%.', array('%user%'   => link_to($post->getUser(), $post->getUser()->getLinkShow()), '%works%' => link_to(__('works'), '@work_education_user?user='.$post->getUser()->getUsername())));
            default:
                return $this->getClassName($post->getObject()).'_'.$post->getType();
        }
    }

    public function getEntityPostText(Post $post)
    {
        $userLink = $this->router->generate($post->getPublisherType().'_show', array('slug' => $post->getPublisher()->getSlug()));
        $userBox = $this->getUserBox($post->getPublisher());
        switch($this->getClassName($post->getObject()).'_'.$post->getType()) {
            case 'Comment_'.Post::TYPE_CREATION:
                return '<a href="'.$userLink.'" '.$userBox.'>'.$post->getPublisher().'</a>';
            // case 'Image_'.Post::TYPE_CREATION:
            //     if (count($post->objectIds()) == 1) {
            //         return $this->translator->trans('%user% added an image.', array(
            //             '%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>'
            //         ));
            //     } else {
            //         return $this->translator->trans('%user% added %count% images.', array(
            //             '%user%' => '<a href="'.$userLink.'">'.$post->getPublisher().'</a>',
            //             '%count%' => count($post->objectIds())
            //         ));
            //     }
            default:
                return $this->getClassName($post->getObject()).'_'.$post->getType();
        }
    }

    function getIcon($object)
    {
        switch ($object) {
            case 'Up':
                return '<span class="glyphicon glyphicon-chevron-up"></span>';
            case 'Down':
                return '<span class="glyphicon glyphicon-chevron-down"></span>';
            case 'Prev':
            case 'Back':
                return '<span class="glyphicon glyphicon-chevron-left"></span>';
            case 'Next':
                return '<span class="glyphicon glyphicon-chevron-right"></span>';
            case 'Remove':
                return '<span class="glyphicon glyphicon-remove"></span>';
                break;
            case 'Event':
            case 'Calendar':
            case 'Event_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-calendar"></span>';
            case 'Disc':
            case 'Disc_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-headphones"></span>';
            case 'Article':
            case 'Review':
            case 'Article_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-print"></span>';
            case 'Link':
            case 'Link_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-bookmark"></span>';
            case 'Image':
            case 'Image_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-picture"></span>';
            case 'Multimedia':
            case 'Multimedia_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-film"></span>';
            case 'Page':
            case 'Page_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-list-alt"></span>';
            case 'Group':
            case 'Group_'.Post::TYPE_CREATION:
            case 'Users':
                return '<span class="glyphicons group"></span>';
            case 'Fan':
            case 'Fan_'.Post::TYPE_FAN_USER:
            case 'Fan_'.Post::TYPE_FAN_GROUP:
            case 'Fan_'.Post::TYPE_FAN_PAGE:
                return '<span class="glyphicon glyphicon-flag"></span>';
            case 'User':
                return '<span class="glyphicon glyphicon-user"></span>';
            case 'User_'.Post::TYPE_REGISTRATION:
                return '<span class="glyphicon-user-add"></span>';
            case 'Biography':
            case 'Biography_'.Post::TYPE_UPDATE:
                return '<span class="glyphicon glyphicon-book"></span>';
            case 'Like':
            case 'Like_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-thumbs-up"></span>';
            case 'Comment':
            case 'Comment_'.Post::TYPE_CREATION:
                return '<span class="glyphicon glyphicon-comment"></span>';
            case 'Comments':
                return '<span class="glyphicons conversation"></span>';
            case 'Wall':
            case 'Post':
                return '<span class="glyphicon glyphicon-th-list"></span>';
            case 'Tag':
                return '<span class="glyphicon glyphicon-tag"></span>';
            case 'Tags':
                return '<span class="glyphicon glyphicon-tags"></span>';
            case 'Work_'.Post::TYPE_EDUCATION:
            case 'Work':
            case 'Job':
            case 'Job_'.Post::TYPE_UPDATE:
                return '<span class="glyphicon glyphicon-briefcase"></span>';
            case 'Education':
            case 'Education_'.Post::TYPE_UPDATE:
                return '<span class="glyphicon glyphicon-book-open"></span>';
            case 'List':
                return '<span class="glyphicon glyphicon-list"></span>';
            case 'Plus':
                return '<span class="glyphicon glyphicon-plus"></span>';
            case 'Minus':
                return '<span class="glyphicon glyphicon-minus"></span>';
            case 'Protagonist':
            case 'Crown':
                return '<span class="glyphicons crown"></span>';
            case 'Ok':
                return '<span class="glyphicon glyphicon-ok"></span>';
            case 'Archive':
                return '<span class="glyphicon glyphicon-folder-close"></span>';
            case 'Time':
                return '<span class="glyphicon glyphicon-time"></span>';
            case 'Map':
                return '<span class="glyphicon glyphicon-map-marker"></span>';
            case 'Edit':
                return '<span class="glyphicon glyphicon-pencil"></span>';
            case 'Alert':
                return '<span class="glyphicon glyphicon-exclamation-sign"></span>';
            case 'Message':
                return '<span class="glyphicon glyphicon-envelope"></span>';
            case 'Globe':
            case 'Notification':
                return '<span class="glyphicon glyphicon-globe"></span>';
            case 'Request':
            case 'Request_in':
                return '<span class="glyphicon glyphicon-bell"></span>';
            case 'Request_out':
                return '<span class="glyphicon glyphicon-share-alt"></span>';
            case 'Relation':
            case 'Relation_'.Post::TYPE_CREATION:
                return '<span class="glyphicons git_branch"></span>';
            case 'Options':
                return '<span class="glyphicons cogwheels"></span>';
            case 'Photo':
                return '<span class="glyphicons camera"></span>';
            case 'Folder':
            case 'Folder_Close':
                return '<span class="glyphicon glyphicon-folder-close"></span>';
            case 'Folder_Open':
                return '<span class="glyphicon glyphicon-folder-open"></span>';
            case 'Info':
                return '<span class="glyphicons pushpin"></span>';
            case 'Fullscreen':
                return '<span class="glyphicon glyphicon-fullscreen"></span>';
            case 'Login':
                return '<span class="glyphicons lock"></span>';
            case 'Sponsored':
                return '<span class="glyphicon glyphicon-bullhorn"></span>';
            case 'Vip':
                return '<span class="glyphicon glyphicon-fire"></span>';
            default:
                return '<span style="color:red;">missing glyphicon for '.$object.'</span>';
        }
    }

    public function getTooltip($what, $options = array())
    {
        if (empty($what) || is_null($what)) {
            return '';
        }

        $options = array_merge(array(
            'placement' => 'top auto',
            'container' => 'body',
            'selector' => null,
            'html' => true,
            'separator' => '<br/>',
            'closure' => null,
            'args' => array(),
            'limit' => 20
        ), $options);

        if (is_array($what) && !is_null($options['limit'])) {
            $what = array_slice($what, 0, $options['limit'], true);
        }

        if (is_array($what) && !is_null($options['closure'])) {
            $closure = create_function('$v, $a', 'return '.$options['closure'].';');
            foreach ($what as &$v) {
                $v = $closure($v, $options[args]);
            }
        }

        if (is_array($what)) {
            $what = join($what, $options['separator']);
        }

        return 'data-toggle="tooltip" data-placement="'.$options['placement'].'" data-container="'.$options['container'].'" data-html="'.($options['html'] ? 'true' : 'false').'" data-title="'.$what.'"';
    }

    public function getModal($options = array())
    {
        $options = array_merge(array(
            'title' => 'false',
            'text' => null,
            'btn1' => null,
            'btn2' => null,
            'btn1Class' => null,
            'btn2Class' => null,
        ), $options);

        $tag = 'confirm';
        foreach ($options as $attr => $value) {
            if (!is_null($value)) {
                $tag .=' data-confirm-'.$attr.'="'.$value.'"';
            }
        }

        return $tag;
    }

    public function getVimeoImage($id, $dim = 'medium')
    {
        $hash = unserialize(file_get_contents('https://vimeo.com/api/v2/video/'.$id.'.php'));

        return $hash[0]['thumbnail_'.$dim];
    }

    public function getSoundcloudImage($id)
    {
        $json = json_decode(file_get_contents('https://api.soundcloud.com/tracks/'.$id.'.json?client_id=69181ee06df52a18c656847d8796d1c0'));

        if (!is_null($json->artwork_url)) {
            return preg_replace('/-[\w\d]+\.jpg/', '-t300x300.jpg', $json->artwork_url);
        } else {
            return $json->waveform_url;
        }
    }

    public function getName()
    {
        return 'cm_extension';
    }
}

*/