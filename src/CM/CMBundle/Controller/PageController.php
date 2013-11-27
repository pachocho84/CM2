<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Entity\PageUser;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Form\PageType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\PageImageType;

/**
 * @Route("/pages")
 */
class PageController extends Controller
{
    /**
     * @Route("/{slug}/wall/{pageNum}", name="page_wall", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function wallAction(Request $request, $slug, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('pageId' => $page->getId()));
        $pagination = $this->get('knp_paginator')->paginate($posts, $pageNum, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination, 'slug' => $page->getSlug()));
        }

        return array('posts' => $pagination, 'page' => $page);
    }

    /**
     * @Route("/{slug}/wall/{lastUpdated}/update", name="page_wall_update")
     * @Route("/{slug}/wall/{lastUpdated}/update", name="page_show_update")
     * @Template("CMBundle:Wall:posts.html.twig")
     */
    public function postsAction(Request $request, $slug, $lastUpdated)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $after = new \DateTime;
        $after->setTimestamp($lastUpdated);
        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('after' => $after, 'pageId' => $page->getId(), 'paginate' => false));

        return array('posts' => $posts);
    }
    
    /**
     * @Route("/new", name="page_new") 
     * @Route("/{slug}/edit", name="page_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        
        if ($slug == null) {
            
            $page = new Page;
            $page->setCreator($user);

            $page->addUser(
                $user,
                true, // admin
                PageUser::STATUS_ACTIVE
            );

            $post = $this->get('cm.post_center')->getNewPost($user, $user);

            $page->addPost($post);
        } else {
            $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
          
            if (!$this->get('cm.user_authentication')->canManage($page)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }
        
        if ($request->get('_route') == 'event_edit') {
            $formRoute = 'event_edit';
            $formRouteArgs = array('id' => $event->getId(), 'slug' => $event->getSlug());
        } else {
            $formRoute = 'event_new';
            $formRouteArgs = array();
        }
 
        $form = $this->createForm(new PageType, $page, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
            'error_bubbling' => false
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($page);

            $em->flush();

            return new RedirectResponse($this->generateUrl('page_show', array('slug' => $page->getSlug())));
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{slug}/account/biography", name="page_biography_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function biographyEditAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($page->getId());
        if (is_null($biography) || !$biography) {
            $biography = new Biography;

            $post = $this->get('cm.post_center')->getNewPost($this->getUser(), $this->getUser());
            $post->setPage($page);

            $biography->addPost($post);
        } else {
            $biography = $biography[0];
        }
 
        $form = $this->createForm(new BiographyType, $biography, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
            'error_bubbling' => false,
            'roles' => $this->getUser()->getRoles(),
            'em' => $em,
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($biography);
            $em->flush();

            return new RedirectResponse($this->generateUrl('page_biography', array('slug' => $page->getSlug())));
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{slug}/biography", name="page_biography")
     * @Template
     */
    public function biographyAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId());
        if (count($biography) == 0) {
            $biography = null;
        } else {
            $biography = $biography[0];
        }

        return array('page' => $page, 'biography' => $biography);
    }

    /**
     * @Route("/{slug}/account/image", name="page_image_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:User:imageEdit.html.twig")
     */
    public function imageEditAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }
 
        $form = $this->createForm(new PageImageType, $page, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($page);
            $em->flush();

            return new RedirectResponse($this->generateUrl('page_show', array('slug' => $page->getSlug())));
        }
        
        return array(
            'form' => $form->createView(),
            'user' => $page
        );
    }
    
    /**
     * @Route("/{slug}/events/{pageNum}", name = "page_events", requirements={"pageNum" = "\d+"})
     * @Route("/{slug}/events/archive/{pageNum}", name="page_events_archive", requirements={"pageNum" = "\d+"}) 
     * @Route("/{slug}/events/category/{category_slug}/{pageNum}", name="page_events_category", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function eventsAction(Request $request, $slug, $pageNum = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
        
        if ($category_slug) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($category_slug, EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
            
        $events = $em->getRepository('CMBundle:Event')->getEvents(array(
            'locale'        => $request->getLocale(), 
            'archive'       => $request->get('_route') == 'page_events_archive' ? true : null,
            'category_id'   => $category_slug ? $category->getId() : null,
            'page_id'      => $page->getId()      
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $pageNum, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'pageNum' => $pageNum));
        }
        
        return array('categories' => $categories, 'page' => $page, 'dates' => $pagination, 'category' => $category, 'pageNum' => $pageNum);
    }

    /**
     * @Route("/{slug}/account/members/{pageNum}", name="page_members_settings", requirements={"pageNum" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")1
     * @Template
     */
    public function membersSettingsAction(Request $request, $slug, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (!$this->get('cm.user_authentication')->isAdminOf($page)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if ($request->isMethod('post')) {

            $tagsUsers = $request->get('tags');

            foreach ($tagsUsers as $pageUserId => $userTags) {
                $em->getRepository('CMBundle:PageUser')->updateUserTags($pageUserId, $userTags);
            }
        }

        $members = $em->getRepository('CMBundle:PageUser')->getMembers($page->getId(), array('paginate' => false, 'limit' => 10, 'status' => array(PageUser::STATUS_ACTIVE, PageUser::STATUS_PENDING)));

        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));
        
        $pagination = $this->get('knp_paginator')->paginate($members, $pageNum, 30);

        return array(
            'page' => $page,
            'members' => $members,
            'tags' => $tags
        );
    }

    /**
     * @Route("/member/promoteAdmin/{id}", name="page_promote_admin", requirements={"id" = "\d+"})
     * @Route("/member/removeAdmin/{id}", name="page_remove_admin", requirements={"id" = "\d+"})
     * @Template("CMBundle:Page:member.html.twig")
     */
    public function adminAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneById($id);

        if (!$this->get('cm.user_authentication')->isAdminOf($pageUser->getPage())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $pageUser->setAdmin(($request->get('_route') == 'page_promote_admin'));
        $em->persist($pageUser);
        $em->flush();

        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));

        return array(
            'member' => $pageUser,
            'tags' => $tags
        );
    }

    /**
     * @Route("/add/{pageId}", name="page_member_add", requirements={"pageId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:Page:member.html.twig")
     */
    public function addAction(Request $request, $pageId)
    {
        $em = $this->getDoctrine()->getManager();

        $userId = $request->get('user_id');

        $this->forward('CMBundle:Request:add', array('object' => 'Page', 'objectId' => $pageId, 'userId' => $userId));

        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneBy(array('pageId' => $pageId, 'userId' => $userId));

        if (!$this->get('cm.user_authentication')->isAdminOf($pageUser->getPage())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));

        return array(
            'member' => $pageUser,
            'tags' => $tags
        );
    }

    /**
     * @Route("/member/remove/{id}", name="page_remove_user", requirements={"id" = "\d+"})
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneById($id);

        if (!$this->get('cm.user_authentication')->isAdminOf($pageUser->getPage())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $request = $em->getRepository('CMBundle:Request')->getRequestFor($pageUser->getUserId(), array('pageId' => $pageUser->getPageId()));

        if (is_null($request)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this. 2', array(), 'http-errors'));
        }

        return $this->forward('CMBundle:Request:delete', array('id'  => $request->getId()));
    }

    /**
     * @Route("/{slug}", name="page_show")
     * @Template
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw new NotFoundHttpException('User not found.');
        }

        $members = $em->getRepository('CMBundle:GroupUser')->getMembers($page->getId(), array('paginate' => false, 'limit' => 10));

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $request = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($this->getUser()->getId(), 'any', array('pageId' => $page->getId()));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId());
        if (count($biography) == 0) {
            $biography = null;
        } else {
            $biography = $biography[0];
        }

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('pageId' => $page->getId()));
        $pagination = $this->get('knp_paginator')->paginate($posts, 1, 15);

        return array('page' => $page, 'members' => $members, 'request' => $request, 'biography' => $biography, 'posts' => $pagination);
    }
}
