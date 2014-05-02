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
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\Education;
use CM\CMBundle\Entity\Tag;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\UserImageType;
use CM\CMBundle\Form\UserTagsType;
use CM\CMBundle\Form\EducationType;
use CM\CMBundle\Form\PageUserCollectionType;

class UserController extends Controller
{
    /**
     * @Route("/users/faces", name="user_faces")
     * @Template
     */
    public function facesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $users = $em->getRepository('CMBundle:User')->getFaces(array('paginate' => false));
        
        return array('users' => $users);
    }
    
    /**
     * @Route("/typeaheadHint", name="user_typeahead_hint")
     */
    public function typeaheadHintAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $exclude = $request->query->get('exclude') ? explode(',', $request->query->get('exclude')) : array();
        $exclude[] = $this->getUser()->getId();
        $users = $em->getRepository('CMBundle:User')->getFromAutocomplete($request->query->get('query'), $this->getUser()->getId(), $exclude);

        $results = array();
        foreach($users as $user)
        {
            $view['id'] = $user['id'];
            $view['value'] = $user['usernameCanonical'];
            $view['label'] = $user['firstName'].' '.$user['lastName'];
            $view['view'] = $this->renderView('CMBundle:User:typeaheadHint.html.twig', array('user' => $user));
            $results[] = $view;
        }

        return new JsonResponse($results);
    }

    /**
     * @Template
     */
    public function menuAction(Request $request)
    {
        $newMessages = $this->getDoctrine()->getManager()->getRepository('CMBundle:MessageThread')->countNew($this->getUser()->getId());
        $newRequests = $this->get('cm.request_center')->getNewRequestsNumber($this->getUser()->getId());
        $newNotifications = $this->get('cm.notification_center')->getNewNotificationsNumber($this->getUser()->getId());

        return array(
            'newMessages' => $newMessages,
            'newRequests' => $newRequests,
            'newNotifications' => $newNotifications
        );
    }

    /**
     * @Route("/notifications/{page}/{perPage}", name="user_notifications", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function notificationsAction(Request $request, $page = 1, $perPage = 6)
    {
        $em = $this->getDoctrine()->getManager();

        $notifications = $em->getRepository('CMBundle:Notification')->getNotifications($this->getUser()->getId());
        $pagination = $this->get('knp_paginator')->paginate($notifications, $page, $perPage);

        $this->get('cm.notification_center')->seeNotifications($this->getUser()->getId());

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:User:notificationList.html.twig', array('notifications' => $pagination));
        }

        return array('notifications' => $pagination);
    }

    /**
     * @Route("/{slug}/wall/{lastUpdated}/update", name="user_show_update")
     * @Template("CMBundle:Wall:posts.html.twig")
     */
    public function wallUpdateAction(Request $request, $slug, $lastUpdated)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }

        $after = new \DateTime;
        $after->setTimestamp($lastUpdated);
        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('after' => $after, 'userId' => $user->getId(), 'paginate' => false));

        return array('posts' => $posts);
    }

    /**
     * @Route("/account/image", name="user_image_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function imageEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
 
        $form = $this->createForm(new UserImageType, $this->getUser(), array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($this->getUser());
            $em->flush();

            return new RedirectResponse($this->generateUrl('user_show', array('slug' => $this->getUser()->getSlug())));
        }
        
        return array(
            'form' => $form->createView(),
            'user' => $this->getUser()
        );
    }

    /**
     * @Route("/{slug}/multimedia/{page}", name="user_multimedia")
     * @Template
     */
    public function multimediasAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }
        
        $multimedias = $em->getRepository('CMBundle:Multimedia')->getMultimedias(array('userId' => $user->getId()));
        $pagination = $this->get('knp_paginator')->paginate($multimedias, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Multimedia:multimedias.html.twig', array(
                'user' => $user,
                'multimedias' => $pagination
            ));
        }
        
        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());

        return array(
            'user' => $user,
            'multimedias' => $pagination,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }

    /**
     * @Route("/{slug}/link/{page}", name="user_link")
     * @Template
     */
    public function linksAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }
        
        $links = $em->getRepository('CMBundle:Link')->getLinks(array('userId' => $user->getId()));
        $pagination = $this->get('knp_paginator')->paginate($links, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Link:links.html.twig', array(
                'user' => $user,
                'links' => $pagination
            ));
        }
        
        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());

        return array(
            'user' => $user,
            'links' => $pagination,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }

    /**
     * @Route("/account/tags", name="user_tags_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function tagsEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
 
        $form = $this->createForm(new UserTagsType, $user, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'tags' => $em->getRepository('CMBundle:Tag')->getTags(array('type' => Tag::TYPE_USER, 'locale' => $request->getLocale()))
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($user);
            $em->flush();

            return new RedirectResponse($this->generateUrl('user_tags_edit'));
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/account/pages/{pageNum}", name = "user_pages", requirements={"pageNum" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function pagesAction(Request $request, $pageNum = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $pageUsers = $em->getRepository('CMBundle:PageUser')->getWithPage($this->getUser()->getId(), array('paginate' => false));

        $form = $this->createForm(new PageUserCollectionType, array('pages' => $pageUsers), array(
            'cascade_validation' => true,
            'type' => 'CM\CMBundle\Form\PageUserJoinType'
        ));

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            foreach ($pageUsers as $pageUser) {
                $em->persist($pageUser);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('user_pages'));
        }
        
        return array(
            'form' => $form->createView(),
            'userPages' => $userPages
        );
    }
    
    /**
     * @Route("/{slug}/events/{page}", name="user_events", requirements={"page" = "\d+"})
     * @Route("/{slug}/events/archive/{page}", name="user_events_archive", requirements={"page" = "\d+"}) 
     * @Route("/{slug}/events/category/{category_slug}/{page}", name="user_events_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function eventsAction(Request $request, $slug, $page = 1, $limit = 10, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
        
        if ($category_slug) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($category_slug, EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
            
        $events = $em->getRepository('CMBundle:Event')->getEvents(array(
            'locale'        => $request->getLocale(), 
            'archive'       => $request->get('_route') == 'event_archive' ? true : null,
            'categoryId'   => $category_slug ? $category->getId() : null,
            'userId'       => $user->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $page, $limit);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }
        
        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());
        
        return array(
            'categories' => $categories,
            'user' => $user,
            'dates' => $pagination,
            'category' => $category,
            'page' => $page,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }
    
    /**
     * @Route("/{slug}/discs/{page}", name="user_discs", requirements={"page" = "\d+"})
     * @Route("/{slug}/discs/archive/{page}", name="user_discs_archive", requirements={"page" = "\d+"}) 
     * @Route("/{slug}/discs/category/{category_slug}/{page}", name="user_discs_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function discsAction(Request $request, $slug, $page = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
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
            'userId'       => $user->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($discs, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Disc:objects.html.twig', array('discs' => $pagination, 'page' => $page));
        }

        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());
        
        return array(
            'categories' => $categories,
            'user' => $user,
            'discs' => $pagination,
            'category' => $category,
            'page' => $page,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }
    
    /**
     * @Route("/{slug}/articles/{page}", name="user_articles", requirements={"page" = "\d+"})
     * @Template
     */
    public function articlesAction(Request $request, $slug, $page = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }
            
        $articles = $em->getRepository('CMBundle:Article')->getArticles(array(
            'locale'        => $request->getLocale(),
            'userId'       => $user->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($articles, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Article:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }

        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());
        
        return array(
            'user' => $user,
            'articles' => $pagination,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }

    /**
     * @Route("/popover/{slug}", name="user_popover")
     * @Template
     */
    public function popoverAction(Request $request, $slug)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException($this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($user->getId(), array('locale' => $request->getLocale()));

        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());

        return array(
            'user' => $user,
            'biography' => $biography,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }

    /**
     * @Route("/{slug}/{page}", name="user_show", requirements={"page" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->getUserBySlug($slug, array('tags' => true, 'locale' => $request->getLocale()));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }
        
        if ($request->isXmlHttpRequest()) {
            /* FETCH */
            // Sponsored 
            $sponsoreds = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Sponsored')->getLessViewed(array('locale' => $request->getLocale())), $page, 2);
            
            // Box partners
            if ($page == 1) {
                $partners = $em->getRepository('CMBundle:HomepageBox')->getBoxes(4, array('locale' => $request->getLocale()));
            }

            // Banners
            $banners = $em->getRepository('CMBundle:HomepageBanner')->getBanners(($page -1) * 2, 2);

            /* ORDER */
            $order = array(
                'bio'   => '0,0,0',
                'events' => '1,1,2',
                'discs'  => '2,2,3',
            );
            $boxes = array();

            if ($page == 1) {
                // Biography
                $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($user->getId(), array('locale' => $request->getLocale()));
                $boxes['biography;'.$order['bio']] = $this->renderView('CMBundle:Biography:box.html.twig', array('publisher' => $user, 'publisherType' => 'user', 'biography' => $biography, 'simple' => true));

                // Next events
                $dates = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Event')->getNextDates(array('userId' => $user->getId(), 'locale' => $request->getLocale())), $page, 3);
                $boxes['events;'.$order['events']] = $this->renderView('CMBundle:Event:nextDates.html.twig', array('dates' => $dates));

                // Next discs
                $discs = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Disc')->getLatests(array('userId' => $user->getId(), 'locale' => $request->getLocale())), $page, 3);
                $boxes['discs;'.$order['discs']] = $this->renderView('CMBundle:Disc:latests.html.twig', array('discs' => $discs));
            }

            /* OTHER POSTS */
            $otherBoxes = array();
            $posts = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('userId' => $user->getId(), 'locale' => $request->getLocale())), $page, 15);
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

        if (is_null($biography)) {
            $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($user->getId(), array('locale' => $request->getLocale()));
        }
        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());

        return array(
            'user' => $user,
            'biography' => $biography,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }
}
