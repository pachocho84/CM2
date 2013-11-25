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
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\PageImageType;

/**
 * @Route("/pages/{slug}")
 */
class PageController extends Controller
{
    /**
     * @Route("/wall/{page}", name="page_wall", requirements={"page" = "\d+"})
     * @Template
     */
    public function wallAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('pageId' => $page->getId()));
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination, 'slug' => $page->getSlug()));
        }

        return array('posts' => $pagination, 'page' => $page);
    }

    /**
     * @Route("/wall/{lastUpdated}/update", name="page_wall_update")
     * @Route("/wall/{lastUpdated}/update", name="page_show_update")
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
     * @Route("/account/biography", name="page_biography_edit")
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
     * @Route("/biography", name="page_biography")
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
     * @Route("/account/image", name="page_image_edit")
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
     * @Route("/events/{page}", name = "page_events", requirements={"page" = "\d+"})
     * @Route("/events/archive/{page}", name="page_events_archive", requirements={"page" = "\d+"}) 
     * @Route("/events/category/{category_slug}/{page}", name="page_events_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function eventsAction(Request $request, $slug, $page = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $_page = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$_page) {
            throw new NotFoundHttpException('Group not found.');
        }
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
        
        if ($category_slug) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($category_slug, EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
            
        $events = $em->getRepository('CMBundle:Event')->getEvents(array(
            'locale'        => $request->getLocale(), 
            'archive'       => $request->get('_route') == '_page_events_archive' ? true : null,
            'category_id'   => $category_slug ? $category->getId() : null,
            'page_id'      => $_page->getId()      
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }
        
        return array('categories' => $categories, 'dates' => $pagination, 'category' => $category, 'page' => $page);
    }

    /**
     * @Route("/", name="page_show")
     * @Template
     */
    public function showAction($slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));

        if (!$page) {
            throw new NotFoundHttpException('User not found.');
        }

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

        return array('page' => $page, 'request' => $request, 'biography' => $biography, 'posts' => $pagination);
    }
}
