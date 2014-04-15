<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use CM\CMBundle\Entity\Group;
use CM\CMBundle\Entity\GroupUser;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Form\GroupType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\GroupImageType;

/**
 * @Route("/groups")
 */
class GroupController extends Controller
{
    /**
     * @Route("/{page}", name = "group_index", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $groups = $em->getRepository('CMBundle:Group')->getGroups();
        $pagination = $this->get('knp_paginator')->paginate($groups, $page, 15);

        return array('groups' => $pagination);
    }

    /**
     * @Route("/{slug}/wall/{page}", name="group_wall", requirements={"page" = "\d+"})
     * @Template
     */
    public function wallAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('groupId' => $group->getId()));
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination, 'slug' => $group->getSlug()));
        }

        return array('posts' => $pagination, 'group' => $group);
    }

    /**
     * @Route("/{slug}/wall/{lastUpdated}/update", name="group_wall_update")
     * @Route("/{slug}/wall/{lastUpdated}/update", name="group_show_update")
     * @Template("CMBundle:Wall:posts.html.twig")
     */
    public function postsAction(Request $request, $slug, $lastUpdated)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        $after = new \DateTime;
        $after->setTimestamp($lastUpdated);
        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('after' => $after, 'groupId' => $group->getId(), 'paginate' => false));

        return array('posts' => $posts);
    }
    
    /**
     * @Route("/new", name="group_new") 
     * @Route("/{slug}/edit", name="group_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        
        if (is_null($slug)) {
            
            $group = new Group;
            $group->setCreator($user);

            $group->addUser(
                $user,
                true, // admin
                GroupUser::STATUS_ACTIVE
            );

            $post = $this->get('cm.post_center')->getNewPost($user, $user);

            $group->addPost($post);
        } else {
            $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
          
            if (!$this->get('cm.user_authentication')->canManage($group)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }
        
        if ($request->get('_route') == 'group_edit') {
            $formRoute = 'group_edit';
            $formRouteArgs = array('id' => $event->getId(), 'slug' => $event->getSlug());
        } else {
            $formRoute = 'group_new';
            $formRouteArgs = array();
        }
 
        $form = $this->createForm(new GroupType, $group, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
            'error_bubbling' => false
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($group);

            $em->flush();

            return new RedirectResponse($this->generateUrl('group_show', array('slug' => $group->getSlug())));
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/account/image", name="group_image_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function imageEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
 
        $form = $this->createForm(new GroupImageType, $this->getGroup(), array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($this->getGroup());
            $em->flush();

            return new RedirectResponse($this->generateUrl('group_show', array('slug' => $this->getGroup()->getSlug())));
        }
        
        return array(
            'form' => $form->createView(),
            'group' => $this->getGroup()
        );
    }

    /**
     * @Route("/{slug}/multimedia/{page}", name="group_multimedia")
     * @Template
     */
    public function multimediasAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));

        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }
        
        $multimedias = $em->getRepository('CMBundle:Multimedia')->getMultimedias(array('groupId' => $group->getId()));
        $pagination = $this->get('knp_paginator')->paginate($multimedias, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Multimedia:multimedias.html.twig', array(
                'group' => $group,
                'multimedias' => $pagination
            ));
        }

        return array(
            'group' => $group,
            'multimedias' => $pagination
        );
    }

    /**
     * @Route("/{slug}/links/{page}", name="group_link")
     * @Template
     */
    public function linksAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));

        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }
        
        $links = $em->getRepository('CMBundle:Link')->getLinks(array('groupId' => $group->getId()));
        $pagination = $this->get('knp_paginator')->paginate($links, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Link:links.html.twig', array(
                'group' => $group,
                'links' => $pagination
            ));
        }

        return array(
            'group' => $group,
            'links' => $pagination
        );
    }
    
    /**
     * @Route("/{slug}/events/{page}", name = "group_events", requirements={"page" = "\d+"})
     * @Route("/{slug}/events/archive/{page}", name="group_events_archive", requirements={"page" = "\d+"}) 
     * @Route("/{slug}/events/category/{category_slug}/{page}", name="group_events_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function eventsAction(Request $request, $slug, $page = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
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
            'archive'       => $request->get('_route') == 'group_events_archive' ? true : null,
            'categoryId'   => $category_slug ? $category->getId() : null,
            'groupId'      => $group->getId()      
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }
        
        return array('categories' => $categories, 'group' => $group, 'dates' => $pagination, 'category' => $category, 'page' => $page);
    }
    
    /**
     * @Route("/{slug}/discs/{page}", name="group_discs", requirements={"page" = "\d+"})
     * @Route("/{slug}/discs/archive/{page}", name="group_discs_archive", requirements={"page" = "\d+"}) 
     * @Route("/{slug}/discs/category/{category_slug}/{page}", name="group_discs_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function discsAction(Request $request, $slug, $page = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
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
            'groupId'       => $group->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($discs, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Disc:objects.html.twig', array('discs' => $pagination));
        }
        
        return array('categories' => $categories, 'group' => $group, 'discs' => $pagination, 'category' => $category);
    }
    
    /**
     * @Route("/{slug}/articles/{page}", name="group_articles", requirements={"page" = "\d+"})
     * @Template
     */
    public function articlesAction(Request $request, $slug, $page = 1, $category_slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }
            
        $articles = $em->getRepository('CMBundle:Article')->getArticles(array(
            'locale'        => $request->getLocale(),
            'groupId'       => $group->getId()       
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($articles, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Article:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }
        
        return array('group' => $group, 'articles' => $pagination);
    }

    /**
     * @Route("/{slug}/account/members/{page}", name="group_members_settings", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")1
     * @Template
     */
    public function membersSettingsAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        if (!$this->get('cm.user_authentication')->isAdminOf($group)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if ($request->isMethod('post')) {

            $tagsUsers = $request->get('tags');

            foreach ($tagsUsers as $groupUserId => $userTags) {
                $em->getRepository('CMBundle:GroupUser')->updateUserTags($groupUserId, $userTags);
            }
        }

        $members = $em->getRepository('CMBundle:GroupUser')->getMembers($group->getId(), array('paginate' => false, 'limit' => 10, 'status' => array(GroupUser::STATUS_ACTIVE, GroupUser::STATUS_PENDING)));

        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));
        
        $pagination = $this->get('knp_paginator')->paginate($members, $page, 30);

        return array(
            'group' => $group,
            'members' => $members,
            'tags' => $tags
        );
    }

    /**
     * @Route("/member/promoteAdmin/{id}", name="group_promote_admin", requirements={"id" = "\d+"})
     * @Route("/member/removeAdmin/{id}", name="group_remove_admin", requirements={"id" = "\d+"})
     * @Template("CMBundle:Group:member.html.twig")
     */
    public function adminAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $groupUser = $em->getRepository('CMBundle:GroupUser')->findOneById($id);

        if (!$this->get('cm.user_authentication')->isAdminOf($groupUser->getGroup())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $groupUser->setAdmin(($request->get('_route') == 'group_promote_admin'));
        $em->persist($groupUser);
        $em->flush();

        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));

        return array(
            'member' => $groupUser,
            'tags' => $tags
        );
    }

    /**
     * @Route("/add/{groupId}", name="group_member_add", requirements={"groupId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:Group:member.html.twig")
     */
    public function addAction(Request $request, $groupId)
    {
        $em = $this->getDoctrine()->getManager();

        $userId = $request->get('user_id');

        $this->forward('CMBundle:Request:add', array('object' => 'Group', 'objectId' => $groupId, 'userId' => $userId));

        $groupUser = $em->getRepository('CMBundle:GroupUser')->findOneBy(array('groupId' => $groupId, 'userId' => $userId));

        if (!$this->get('cm.user_authentication')->isAdminOf($groupUser->getGroup())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));

        return array(
            'member' => $groupUser,
            'tags' => $tags
        );
    }

    /**
     * @Route("/member/remove/{id}", name="group_remove_user", requirements={"id" = "\d+"})
     */
    public function removeAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $groupUser = $em->getRepository('CMBundle:GroupUser')->findOneById($id);

        if (!$this->get('cm.user_authentication')->isAdminOf($groupUser->getGroup())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $request = $em->getRepository('CMBundle:Request')->getRequestFor($groupUser->getUserId(), array('groupId' => $groupUser->getGroupId()));

        if (is_null($request)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this. 2', array(), 'http-errors'));
        }

        return $this->forward('CMBundle:Request:delete', array('id'  => $request->getId()));
    }

    /**
     * @Route("/{groupId}/join/{object}/{userId}", name="group_change_join", requirements={"groupId" = "\d+", "userId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:GroupUser:joinType.html.twig")
     */
    public function joinAction(Request $request, $groupId, $object, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        $groupUser = $em->getRepository('CMBundle:GroupUser')->findOneBy(array('userId' => $userId, 'groupId' => $groupId));

        switch ($object) {
            case 'Event':
                $groupUser->setJoinEvent($request->get('joinEvent'));
                break;
            case 'Disc':
                $groupUser->setJoinDisc($request->get('joinDisc'));
                break;
        }

        if (!$this->get('validator')->validate($groupUser)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->persist($groupUser);
        $em->flush();

        return array(
            'group' => $em->getRepository('CMBundle:Group')->getGroups(array('userId' => $userId, 'groupId' => $groupId, 'paginate' => false, 'limit' => 1))[0],
            'object' => $object
        );
    }

    /**
     * @Route("/{slug}/delete", name="group_delete")
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));

        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        if ($this->get('cm.user_authentication')->canManage($group)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->getRepository('CMBundle:Group')->remove($group);

        return $this->redirect($this->generateUrl('user_show', array('slug'  => $this->getUser()->getSlug())), 301);
    }

    /**
     * @Route("/popover/{slug}", name="group_popover")
     * @Template
     */
    public function popoverAction(Request $request, $slug)
    {
        if (!$request->isXmlHttpRequest()) {
            throw new NotFoundHttpException($this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getGroupBiography($group->getId(), array('locale' => $request->getLocale()));

        return array('group' => $group, 'biography' => $biography);
    }

    /**
     * @Route("/{slug}/{page}", name="group_show", requirements={"page" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));

        if (!$group) {
            throw new NotFoundHttpException('User not found.');
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
                'login'  => '0,1,2',
                'last'   => '1,0,0',
                'rev'    => '5,2,1',
                'spo0'   => '4,8,3',
                'spo1'   => '11,14,7',
                'ban0'   => '6,9,11',
                'ban1'   => '15,12,15',
                'vip0'   => '10,10,12',
                'vip1'   => '13,13,13',
                'par0'   => '3,6,4',
                'par1'   => '8,7,9',
                'par2'   => '12,11,10',
                'par3'   => '14,15,14',
                'events' => '2,3,5',
                'discs'  => '9,4,6',
                'sugg'   => '7,5,8'
            );
            $boxes = array();

            // Last registered users 
            if ($page == 1) {
                // Next events
                $dates = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Event')->getNextDates(array('groupId' => $group->getId(), 'locale' => $request->getLocale())), $page, 3);
                $boxes['events;'.$order['events']] = $this->renderView('CMBundle:Event:nextDates.html.twig', array('dates' => $dates));

                // Next discs
                $discs = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Disc')->getLatests(array('groupId' => $group->getId(), 'locale' => $request->getLocale())), $page, 3);
                $boxes['discs;'.$order['discs']] = $this->renderView('CMBundle:Disc:latests.html.twig', array('discs' => $discs));

                // Sponsored
                foreach ($sponsoreds as $i => $sponsored) {
                    $boxes['sponsored_'.$sponsored->getId().';'.$order['spo'.$i]] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $sponsored->getEntity()->getPost(), 'postType' => 'sponsored'));
                }

                // Banners
                foreach ($banners as $i => $banner) {
                    $boxes['banner_'.$banner->getId().';'.$order['ban'.$i]] = $this->renderView('CMBundle:Wall:boxBanner.html.twig', array('banner' => $banner));
                }

            }

            /* OTHER POSTS */
            $posts = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('groupId' => $group->getId(), 'locale' => $request->getLocale())), $page, 15);
            foreach ($posts as $post) {
                $boxes['post_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post));
            }

            $boxes['loadMore'] = $this->renderView('CMBundle:Wall:loadMore.html.twig', array('paginationData' => $posts->getPaginationData()));

            return new JsonResponse($boxes);
        }

        $members = $em->getRepository('CMBundle:GroupUser')->getMembers($group->getId(), array('paginate' => false, 'limit' => 10));

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $req = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($this->getUser()->getId(), 'any', array('groupId' => $group->getId()));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getGroupBiography($group->getId());
        if (count($biography) == 0) {
            $biography = null;
        } else {
            $biography = $biography[0];
        }

        return array('group' => $group, 'members' => $members, 'request' => $req, 'biography' => $biography, 'posts' => $pagination);
    }
}
