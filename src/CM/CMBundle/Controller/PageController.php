<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Entity\PageUser;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Tag;
use CM\CMBundle\Form\PageType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\PageImageType;


class PageController extends Controller
{
    /**
     * @Route("/pages/{page}", name = "page_index", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pages = $em->getRepository('CMBundle:Page')->getPages(array('biography' => true, 'locale' => $request->getLocale()));
        $pagination = $this->get('knp_paginator')->paginate($pages, $page, 15);

        return array('pages' => $pagination);
    }

    /**
     * @Route("/pages/{slug}/wall/{pageNum}", name="page_wall", requirements={"pageNum" = "\d+"})
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
     * @Route("/pages/{slug}/wall/{lastUpdated}/update", name="page_wall_update")
     * @Route("/pages/{slug}/wall/{lastUpdated}/update", name="page_show_update")
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
     * @Route("/pages/new", name="page_new") 
     * @Route("/account/pages/{slug}/edit", name="page_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        
        if (is_null($slug)) {
            $page = new Page;
            $page->setCreator($user);

            $page->addUser(
                $user,
                true, // admin
                PageUser::STATUS_ACTIVE
            );

            $post = $this->get('cm.post_center')->getNewPost($user, $user);

            $page->addPost($post);

            $biography = new Biography;

            $bioPost = $this->get('cm.post_center')->getNewPost($user, $user);
            $bioPost->setPage($page);
            $biography->setPost($bioPost);

            $page->setBiography($biography);
        } else {
            $page = $em->getRepository('CMBundle:Page')->getPage($slug, array('tags' => true, 'pageUsers' => true, 'biography' => true));

            if (!$this->get('cm.user_authentication')->isAdminOf($page)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }
 
        $form = $this->createForm(new PageType, $page, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'roles' => $this->getUser()->getRoles(),
            'em' => $em,
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale(),
            'tags' => $em->getRepository('CMBundle:Tag')->getTags(array('type' => Tag::TYPE_PAGE, 'locale' => $request->getLocale())),
        ))->add('save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($page);

            $em->flush();

            return new RedirectResponse($this->generateUrl('page_show', array('slug' => $page->getSlug())));
        }
        
        return array(
            'page' => $page,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/pages/{slug}/multimedia/{pageNum}", name="page_multimedia")
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
     * @Route("/pages/{slug}/links/{pageNum}", name="page_link")
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
     * @Route("/pages/{slug}/events/{pageNum}", name = "page_events", requirements={"pageNum" = "\d+"})
     * @Route("/pages/{slug}/events/archive/{pageNum}", name="page_events_archive", requirements={"pageNum" = "\d+"}) 
     * @Route("/pages/{slug}/events/category/{category_slug}/{pageNum}", name="page_events_category", requirements={"pageNum" = "\d+"})
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
     * @Route("/pages/{slug}/discs/{pageNum}", name="page_discs", requirements={"pageNum" = "\d+"})
     * @Route("/pages/{slug}/discs/archive/{pageNum}", name="page_discs_archive", requirements={"pageNum" = "\d+"}) 
     * @Route("/pages/{slug}/discs/category/{category_slug}/{pageNum}", name="page_discs_category", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function discsAction(Request $request, $slug, $pageNum = 1, $category_slug = null)
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
     * @Route("/pages/{slug}/articles/{pageNum}", name="page_articles", requirements={"pageNum" = "\d+"})
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
     * @Route("/pages/{pageId}/join/{object}/{userId}", name="page_change_join", requirements={"pageId" = "\d+", "userId" = "\d+"})
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
     * @Route("/pages/{slug}/delete", name="page_delete")
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
     * @Route("/pages/popover/{slug}", name="page_popover")
     * @Template
     */
    public function popoverAction(Request $request, $slug)
    {
        // if (!$request->isXmlHttpRequest()) {
        //     throw new NotFoundHttpException($this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        // }

        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->getPage($slug, array('tags' => true));

        if (!$page) {
            throw new NotFoundHttpException($this->get('translator')->trans('Page not found.', array(), 'http-errors'));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId(), array('locale' => $request->getLocale()));

        return array('page' => $page, 'biography' => $biography);
    }

    /**
     * @Route("/pages/{slug}/{pageNum}", name="page_show", requirements={"pageNum" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $slug, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->getPage($slug, array('tags' => true, 'pageUser' => is_null($this->getUser()) ?: $this->getUser()->getId()));

        if (!$page) {
            throw new NotFoundHttpException('User not found.');
        }
        
        if ($request->isXmlHttpRequest()) {
            /* FETCH */
            // Sponsored 
            $sponsoreds = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Sponsored')->getLessViewed(array('locale' => $request->getLocale())), $pageNum, 2);
            
            // Box partners
            if ($pageNum == 1) {
                $partners = $em->getRepository('CMBundle:HomepageBox')->getBoxes(4, array('locale' => $request->getLocale()));
            }

            // Banners
            $banners = $em->getRepository('CMBundle:HomepageBanner')->getBanners(($pageNum -1) * 2, 2);

            /* ORDER */
            $order = array(
                'bio'   => '0,0,0',
                'events' => '1,1,2',
                'discs'  => '2,2,3',
            );
            $boxes = array();

            if ($pageNum == 1) {
                // Biography
                $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId(), array('locale' => $request->getLocale()));
                $boxes['biography;'.$order['bio']] = $this->renderView('CMBundle:Biography:box.html.twig', array('publisher' => $page, 'publisherType' => 'page', 'biography' => $biography, 'simple' => true));

                // Next events
                $dates = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Event')->getNextDates(array('pageId' => $page->getId(), 'locale' => $request->getLocale())), $pageNum, 3);
                $boxes['events;'.$order['events']] = $this->renderView('CMBundle:Event:nextDates.html.twig', array('dates' => $dates));

                // Next discs
                $discs = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Disc')->getLatests(array('pageId' => $page->getId(), 'locale' => $request->getLocale())), $pageNum, 3);
                $boxes['discs;'.$order['discs']] = $this->renderView('CMBundle:Disc:latests.html.twig', array('discs' => $discs));
            }

            /* OTHER POSTS */
            $otherBoxes = array();
            $posts = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('pageId' => $page->getId(), 'locale' => $request->getLocale())), $pageNum, 15);
            foreach ($posts as $post) {
                $otherBoxes['post_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post));
            }

            /* INSERT IN OTHER POSTS */
            // sponsoreds
            $indexes = range(0, count($otherBoxes));
            shuffle($indexes);
            $sponsoredIndexes = array_slice($indexes, 0, count($sponsoreds));
            sort($sponsoredIndexes);
            foreach ($sponsoreds as $sponsored) {
                $index = array_shift($sponsoredIndexes);
                $box = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $sponsored->getEntity()->getPost(), 'postType' => 'sponsored'));
                array_splice($otherBoxes, $index, 0, $box);
            }
            
            // banners
            $indexes = range(0, count($otherBoxes));
            shuffle($indexes);
            $bannerIndexes = array_slice($indexes, 0, count($banners));
            sort($bannerIndexes);
            foreach ($banners as $banner) {
                $index = array_shift($bannerIndexes);
                $box = $this->renderView('CMBundle:Wall:boxBanner.html.twig', array('banner' => $banner));
                array_splice($otherBoxes, $index, 0, $box);
            }

            $boxes = array_merge($boxes, $otherBoxes);

            $boxes['loadMore'] = $this->renderView('CMBundle:Wall:loadMore.html.twig', array('paginationData' => $posts->getPaginationData()));

            return new JsonResponse($boxes);
        }

        $members = $em->getRepository('CMBundle:PageUser')->getMembers($page->getId(), array('paginate' => false, 'limit' => 10));

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $req = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($this->getUser()->getId(), 'any', array('pageId' => $page->getId()));
        }

        if (is_null($biography)) {
            $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId(), array('locale' => $request->getLocale()));
        }

        return array('page' => $page, 'members' => $members, 'request' => $req, 'biography' => $biography, 'posts' => $pagination);
    }
}
