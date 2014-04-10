<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Form\CommentType;
use CM\CMBundle\Entity\HomepageBox;

/**
 * @Route("")
 */
class WallController extends Controller
{
    /**
     * @Route("/{page}", name="wall_index", requirements={"page"="\d+"})
     * @Route("/{page}", name="home", requirements={"page"="\d+"})
     * @Route("/vips/{page}", name="wall_vips")
     * @Route("/fans/{page}", name="wall_fans")
     * @Route("/connections/{page}", name="wall_connections")
     * @Route("/editorial/{page}", name="wall_editorial")
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $boxes = array();

            /* Last registered users */
            if ($page == 1) {
                $boxes['lastUsers;left'] = $this->renderView('CMBundle:Wall:boxLastUsers.html.twig', array('lastUsers' => $em->getRepository('CMBundle:User')->getLastRegisteredUsers(28)));
            }

            /* Suggested users or Login/Register box */
            if ($page != 1) {
                // do nothing
            } elseif ($this->get('security.context')->isGranted('ROLE_USER')) {
                $relationTypes = $em->getRepository('CMBundle:RelationType')->findBy(array());
                $boxes['suggestedUsers;left'] = $this->renderView('CMBundle:Wall:boxSuggested.html.twig', array('suggestions' => $em->getRepository('CMBundle:Relation')->getSuggestedUsers($this->getUser()->getId(), 0, 5, true), 'relationTypes' => $relationTypes));
            } else {
                $boxes['login_register;right'] = $this->renderView('CMBundle:Wall:boxAuthentication.html.twig');
            }

            /* Next events */
            if ($page == 1 && $request->get('_route') == 'wall_index') {
                $dates = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Event')->getNextDates(array('locale' => $request->getLocale())), $page, 3);
                $boxes['dates;right'] = $this->renderView('CMBundle:Event:nextDates.html.twig', array('dates' => $dates));
            }

            /* Sponsored */
            $sponsoreds = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Sponsored')->getLessViewed(array('locale' => $request->getLocale())), $page, 2);
            foreach ($sponsoreds as $sponsored) {
                $boxes['sponsored_'.$sponsored->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $sponsored->getEntity()->getPost(), 'postType' => 'sponsored'));
            }

            /* Box partners */
            if ($page == 1 && $request->get('_route') == 'wall_index') {
                $wallBoxes = $em->getRepository('CMBundle:HomepageBox')->getBoxes(4, array('locale' => $request->getLocale()));
                foreach ($wallBoxes as $box) {
                    switch ($box->getType()) {
                        case HomepageBox::TYPE_EVENT:
                            $objects = $em->getRepository('CMBundle:Event')->getNextDates(array('pageId' => $box->getPageId(), 'locale' => $request->getLocale()));
                            $limit = 5;
                            break;
                        case HomepageBox::TYPE_DISC:
                            $objects = $em->getRepository('CMBundle:Disc')->getDiscs(array('pageId' => $box->getPageId(), 'locale' => $request->getLocale()));
                            $limit = 6;
                            break;
                        case HomepageBox::TYPE_ARTICLE:
                            $objects = $em->getRepository('CMBundle:Disc')->getArticles(array('pageId' => $box->getPageId(), 'locale' => $request->getLocale()));
                            $limit = 5;
                            break;
                        case HomepageBox::TYPE_RUBRIC:
                            $objects = $em->getRepository('CMBundle:HomepageArchive')->getArticles($box->getCategoryId(), array('locale' => $request->getLocale()));
                            $limit = 3;
                            break;
                    }
                    $objects = $this->get('knp_paginator')->paginate($objects, $page, $limit);

                    if (empty($objects->getItems()) && $box->getType() != HomepageBox::TYPE_RUBRIC) {
                        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($box->getPageId(), array('locale' => $request->getLocale()));
                    }

                    $boxes['partners_'.$box->getPosition()] = $this->renderView('CMBundle:Wall:boxPartner.html.twig', array('box' => $box, 'objects' => $objects, 'biography' => $biography));
                }
            }

            /* Vips */
            if (in_array($request->get('_route'), array('wall_index', 'wall_vips'))) {
                $vips = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('vip' => true, 'entityCreation' => true, 'locale' => $request->getLocale())), $page, 2);
                foreach ($vips as $post) {
                    $boxes['vip_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post, 'postType' => 'vip'));
                }
            }

            /* Reviews */
            if ($page == 1 && in_array($request->get('_route'), array('wall_index', 'wall_newspaper'))) {
                if ($page == 1) {
                    $reviews = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:HomepageArchive')->getLastReviews(array('locale' => $request->getLocale())), $page, 4);
                    $boxes['reviews'] = $this->renderView('CMBundle:Wall:boxReviews.html.twig', array('reviews' => $reviews));
                }
            }

            /* Banners */
            $banners = $em->getRepository('CMBundle:HomepageBanner')->getBanners(($page -1) * 2, 2);
            foreach ($banners as $banner) {
                $boxes['banner_'.$banner->getId()] = $this->renderView('CMBundle:Wall:boxBanner.html.twig', array('banner' => $banner));
            }

            /* Box fans */
            if ($this->get('security.context')->isGranted('ROLE_USER') && $request->get('_route') == 'wall_fans') {
                $fansIds = $em->getRepository('CMBundle:Fan')->getFans($this->getUser()->getId(), true);
                if (!empty($fansIds)) {
                    $in = array('inUsers' => array(), 'inPages' => array(), 'inGroups' => array());
                    foreach ($fansIds as $fan) {
                        if (!is_null($fan->getUserId())) {
                            $in['inUsers'][] = $fan->getUserId();
                        } elseif (!is_null($fan->getPageId())) {
                            $in['inPages'][] = $fan->getPageId();
                        } elseif (!is_null($fan->getGroupId())) {
                            $in['inGroups'][] = $fan->getGroupId();
                        }
                    }
                    $fans = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('in' => $in, 'locale' => $request->getLocale())), $page, 30);
                    foreach ($fans as $post) {
                        $boxes['fan_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post));
                    }
                }
            }

            /* Box connections */
            if ($this->get('security.context')->isGranted('ROLE_USER') && $request->get('_route') == 'wall_connections') {
                $relationsIds = $em->getRepository('CMBundle:Relation')->getRelationsIdsPerUser($this->getUser()->getId());
                if (!empty($relationsIds)) {
                    $relationsIds = array_map(function($v) { return $v['fromUserId']; }, $relationsIds);
                    $connections = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('inUsers' => $relationsIds, 'locale' => $request->getLocale())), $page, 30);
                    foreach ($connections as $post) {
                        $boxes['connection_'.$post->getId()] = $this->renderView('CMBundle:Wall:boxPost.html.twig', array('post' => $post));
                    }
                }
            }

            /* Posts */
            if ($request->get('_route') == 'wall_index') {
                $posts = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('locale' => $request->getLocale())), $page, 15);
                foreach ($posts as $post) {
                    $boxes['post_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post));
                }
            }

            switch ($request->get('_route')) {
                case 'wall_index':
                    $paginationData = $posts->getPaginationData();
                    break;
                case 'wall_vips':
                    $paginationData = $vips->getPaginationData();
                    break;
                case 'wall_fans':
                    $paginationData = $fans->getPaginationData();
                    break;
                case 'wall_connections':
                    $paginationData = $connections->getPaginationData();
                    break;
            }
            $boxes['loadMore'] = $this->renderView('CMBundle:Wall:loadMore.html.twig', array('paginationData' => $paginationData));

            return new JsonResponse($boxes);
        }

        return array();
    }
    
    /**
     * @Route("/wall/entity/{id}/{page}", name="wall_entity", requirements={"id" = "\d+", "page" = "\d+"})
     * @Route("/wall/entity/{id}/{lastUpdated}/update", name="wall_entity_update")
     * @Route("/wall/entity/update", name="_update")
     * @Route("/wall/entity/update", name="")
     * @Template
     */
    public function entityAction(Request $request, $id, $page = 1, $lastUpdated = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (is_null($lastUpdated)) {
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('entityId' => $id, 'locale' => $request->getLocale(), 'aggregate' => false));
        } else {
            $after = new \DateTime;
            $after->setTimestamp($lastUpdated);
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('entityId' => $id, 'locale' => $request->getLocale(), 'aggregate' => false, 'after' => $after));
        }
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 10);

        $comment = new Comment;
        $form = $this->createForm(new CommentType, $comment, array(
            'action' => $this->generateUrl('comment_entity_new', array(
                'id' => $pagination[0]->getEntity()->getPost()->getId()
            )),
            'cascade_validation' => true
        ));

        $form = $form->createView();
                
        return array(
            'posts' => $pagination,
            'inEntity' => true,
            'commentForm' => $form
        );
    }

    /**
     * @Route("/wall/{lastUpdated}/update", name="wall_index_update")
     * @Template
     */
    public function postsAction(Request $request, $lastUpdated)
    {
        $em = $this->getDoctrine()->getManager();

        $after = new \DateTime;
        $after->setTimestamp($lastUpdated);
        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('after' => $after, 'paginate' => false));

        return array('posts' => $posts);
    }

    /**
     * @Route("/pages/{slug}/wall/{page}", name="wall_page", requirements={"page" = "\d+"})
     * @Template
     */
    public function pageAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException($this->get('translator')->trans('Page not found.', array(), 'http-errors'));
        }
        
        if ($request->isXmlHttpRequest()) {
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('pageId' => $page->getId()));
            $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
            
            if ($page == 1) {
                return $this->render('CMBundle:Wall:box.html.twig', array(
                    'posts' => $pagination,
                    'slug' => $page->getSlug(),
                    'simple' => $request->get('simple'),
                    'link' => $this->generateUrl('wall_page', array(
                        'slug' => $slug
                    ))
                ));
            } else {
                return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination, 'slug' => $page->getSlug()));
            }
        }

        return array('page' => $page);
    }

    /**
     * @Route("/groups/{slug}/wall/{group}", name="wall_group", requirements={"group" = "\d+"})
     * @Template
     */
    public function groupAction(Request $request, $slug, $group = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
        }
        
        if ($request->isXmlHttpRequest()) {
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('groupId' => $group->getId()));
            $pagination = $this->get('knp_paginator')->paginate($posts, $group, 15);
            
            if ($group == 1) {
                return $this->render('CMBundle:Wall:box.html.twig', array(
                    'posts' => $pagination,
                    'slug' => $group->getSlug(),
                    'simple' => $request->get('simple'),
                    'link' => $this->generateUrl('wall_group', array(
                        'slug' => $slug
                    ))
                ));
            } else {
                return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination, 'slug' => $group->getSlug()));
            }
        }

        return array('group' => $group);
    }

    /**
     * @Route("/{slug}/wall/{page}", name="wall_user", requirements={"page" = "\d+"})
     * @Template
     */
    public function userAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }
        
        if ($request->isXmlHttpRequest()) {
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('userId' => $user->getId()));
            $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
            
            if ($page == 1) {
                return $this->render('CMBundle:Wall:box.html.twig', array(
                    'posts' => $pagination,
                    'slug' => $user->getSlug(),
                    'simple' => $request->get('simple'),
                    'link' => $this->generateUrl('wall_user', array(
                        'slug' => $slug
                    ))
                ));
            } else {
                return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination, 'slug' => $user->getSlug()));
            }
        }

        return array('user' => $user);
    }

    /**
     * @Route("/banners/{count}", name="wall_banner", requirements={"count" = "\d+"})
     * @Template
     */
    public function bannersAction(Request $request, $count = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $banners = $em->getRepository('CMBundle:HomepageBanner')->getRandBanners($count);

        return array('banners' => $banners);
    }

    /**
     * @Route("/wall/show/{postId}", name="wall_show", requirements={"postId" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $postId)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('CMBundle:Post')->getPostWithSocial($postId);

        return array('post' => $post);
    }

    // /**
    //  * @Route("/ping")
    //  */
    // public function pingAction()
    // {
    //     return new Response;
    // }
}