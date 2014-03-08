<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
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
     * @Route("/{page}", name = "page_index", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pages = $em->getRepository('CMBundle:Page')->getPages();
        $pagination = $this->get('knp_paginator')->paginate($pages, $page, 15);

        return array('pages' => $pagination);
    }
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
     * @Route("/account/image", name="page_image_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function imageEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
 
        $form = $this->createForm(new PageImageType, $this->getPage(), array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($this->getPage());
            $em->flush();

            return new RedirectResponse($this->generateUrl('page_show', array('slug' => $this->getPage()->getSlug())));
        }
        
        return array(
            'form' => $form->createView(),
            'page' => $this->getPage()
        );
    }

    /**
     * @Route("/{slug}/multimedia/{pageNum}", name="page_multimedia")
     * @Template
     */
    public function multimediasAction(Request $request, $slug, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }
        
        $multimedias = $em->getRepository('CMBundle:Multimedia')->getMultimedias(array('pageId' => $page->getId()));
        $pagination = $this->get('knp_paginator')->paginate($multimedias, $pageNum, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Multimedia:multimediaList.html.twig', array(
                'page' => $page,
                'multimedias' => $pagination
            ));
        }

        return array(
            'page' => $page,
            'multimedias' => $pagination
        );
    }

    /**
     * @Route("/{slug}/links/{pageNum}", name="page_link")
     * @Template
     */
    public function linksAction(Request $request, $slug, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }
        
        $links = $em->getRepository('CMBundle:Link')->getLinks(array('pageId' => $page->getId()));
        $pagination = $this->get('knp_paginator')->paginate($links, $pageNum, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Link:linkList.html.twig', array(
                'page' => $page,
                'links' => $pagination
            ));
        }

        return array(
            'page' => $page,
            'links' => $pagination
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
            'categoryId'   => $category_slug ? $category->getId() : null,
            'pageId'      => $page->getId()      
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $pageNum, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'pageNum' => $pageNum));
        }
        
        return array('categories' => $categories, 'page' => $page, 'dates' => $pagination, 'category' => $category, 'pageNum' => $pageNum);
    }
    
    /**
     * @Route("/{slug}/discs/{pageNum}", name="page_discs", requirements={"pageNum" = "\d+"})
     * @Route("/{slug}/discs/archive/{pageNum}", name="page_discs_archive", requirements={"pageNum" = "\d+"}) 
     * @Route("/{slug}/discs/category/{category_slug}/{pageNum}", name="page_discs_category", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function discsAction(Request $request, $slug, $pageNum = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Group not found.');
        }
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
        
        if ($category_slug) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($category_slug, EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
            
        $discs = $em->getRepository('CMBundle:Disc')->getDiscs(array(
            'locale'        => $request->getLocale(),
            'categoryId'   => $category_slug ? $category->getId() : null,
            'pageId'       => $page->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($discs, $pageNum, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Disc:objects.html.twig', array('discs' => $pagination, 'pageNum' => $pageNum));
        }
        
        return array('categories' => $categories, 'page' => $page, 'discs' => $pagination, 'category' => $category, 'pageNum' => $pageNum);
    }
    
    /**
     * @Route("/{slug}/articles/{pageNum}", name="page_articles", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function articlesAction(Request $request, $slug, $pageNum = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }
            
        $articles = $em->getRepository('CMBundle:Article')->getArticles(array(
            'locale'        => $request->getLocale(),
            'pageId'       => $page->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($articles, $pageNum, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Article:objects.html.twig', array('dates' => $pagination, 'pageNum' => $pageNum));
        }
        
        return array('page' => $page, 'articles' => $pagination, 'pageNum' => $pageNum);
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
     * @Route("/{pageId}/join/{object}/{userId}", name="page_change_join", requirements={"pageId" = "\d+", "userId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:PageUser:joinType.html.twig")
     */
    public function joinAction(Request $request, $pageId, $object, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneBy(array('userId' => $userId, 'pageId' => $pageId));

        switch ($object) {
            case 'Event':
                $pageUser->setJoinEvent($request->get('joinEvent'));
                break;
            case 'Disc':
                $pageUser->setJoinDisc($request->get('joinDisc'));
                break;
        }

        if (!$this->get('validator')->validate($pageUser)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->persist($pageUser);
        $em->flush();

        return array(
            'page' => $em->getRepository('CMBundle:Page')->getPages(array('userId' => $userId, 'pageId' => $pageId, 'paginate' => false, 'limit' => 1))[0],
            'object' => $object
        );
    }

    /**
     * @Route("/{slug}/delete", name="page_delete")
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        if ($this->get('cm.user_authentication')->canManage($page)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->getRepository('CMBundle:Page')->remove($page);

        return $this->redirect($this->generateUrl('user_show', array('slug'  => $this->getUser()->getSlug())), 301);
    }

    /**
     * @Route("/{slug}/{pageNum}", name="page_show", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $slug, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw new NotFoundHttpException('User not found.');
        }

        $members = $em->getRepository('CMBundle:GroupUser')->getMembers($page->getId(), array('paginate' => false, 'limit' => 10));

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $req = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($this->getUser()->getId(), 'any', array('pageId' => $page->getId()));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId(), array('locale' => $request->getLocale()));

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('pageId' => $page->getId()));
        $pagination = $this->get('knp_paginator')->paginate($posts, $pageNum, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('slug' => $page->getSlug(), 'posts' => $pagination, 'page' => $pageNum));
        }

        return array('page' => $page, 'members' => $members, 'request' => $req, 'biography' => $biography, 'posts' => $pagination);
    }
}
