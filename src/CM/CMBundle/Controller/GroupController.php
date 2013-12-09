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
        
        if ($request->get('_route') == 'event_edit') {
            $formRoute = 'event_edit';
            $formRouteArgs = array('id' => $event->getId(), 'slug' => $event->getSlug());
        } else {
            $formRoute = 'event_new';
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
     * @Route("/{slug}/account/biography", name="group_biography_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function biographyEditAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($group->getId());
        if (is_null($biography) || !$biography) {
            $biography = new Biography;

            $post = $this->get('cm.post_center')->getNewPost($this->getUser(), $this->getUser());
            $post->setGroup($group);

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

            return new RedirectResponse($this->generateUrl('group_biography', array('slug' => $group->getSlug())));
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{slug}/biography", name="group_biography")
     * @Template
     */
    public function biographyAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        $biography = $em->getRepository('CMBundle:Biography')->getGroupBiography($group->getId());
        if (count($biography) == 0) {
            $biography = null;
        } else {
            $biography = $biography[0];
        }

        return array('group' => $group, 'biography' => $biography);
    }

    /**
     * @Route("/{slug}/images/{page}", name="group_images", requirements={"page" = "\d+"})
     * @Template
     */
    public function imagesAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
        }

        $images = $em->getRepository('CMBundle:Image')->getImages(array('groupId' => $group->getId()));
        
        $pagination = $this->get('knp_paginator')->paginate($images, $page, 32);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:imageList.html.twig', array(
                'group' => $group,
                'images' => $pagination
            ));
        }

        return array(
            'group' => $group,
            'images' => $pagination
        );
    }

    /**
     * @Route("/{slug}/image/{id}/{page}", name="group_image", requirements={"id" = "\d+", "page" = "\d+"})
     * @Template
     */
    public function imageAction(Request $request, $slug, $id, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
        }

        try {
            $image = $em->getRepository('CMBundle:Image')->getImage($id, array('groupId' => $group->getId()));
        } catch (\Exception $e) {
            throw new NotFoundHttpException($this->get('translator')->trans('Image not found.', array(), 'http-errors'));
        }

        return array(
            'group' => $group,
            'image' => $image
        );
    }

    /**
     * @Route("/{slug}/albums/{page}", name="group_albums", requirements={"page" = "\d+"})
     * @Template
     */
    public function albumsAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
        }

        $albums = $em->getRepository('CMBundle:ImageAlbum')->getAlbums(array(
            'groupId' => $group->getId(),
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($albums, $page, 32);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:albumList.html.twig', array(
                'group' => $group,
                'albums' => $pagination
            ));
        }

        return array(
            'group' => $group,
            'albums' => $pagination
        );
    }

    /**
     * @Route("/{slug}/album/{id}/{page}", name="group_album", requirements={"id" = "\d+", "page" = "\d+"})
     * @Template
     */
    public function albumAction(Request $request, $slug, $id, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
        
        if (!$group) {
            throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
        }

        try {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getAlbum($id, array('groupId' => $group->getId()));
        } catch (\Exception $e) {
            throw new NotFoundHttpException($this->get('translator')->trans('Album not found.', array(), 'http-errors'));
        }

        $images = $em->getRepository('CMBundle:Image')->getImages(array('albumId' => $id));
        
        $pagination = $this->get('knp_paginator')->paginate($images, $page, 32);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:imageList.html.twig', array(
                'group' => $group,
                'album' => $album,
                'images' => $pagination
            ));
        }

        return array(
            'group' => $group,
            'album' => $album,
            'images' => $pagination
        );
    }

    /**
     * @Route("/{slug}/images/entities/{page}", name="group_entities_albums", requirements={"page" = "\d+"})
     * @Template
     */
    public function imagesEntitiesAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $group = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));

        if (!$group) {
            throw new NotFoundHttpException('Group not found.');
        }

        $entities = $em->getRepository('CMBundle:Image')->getEntityImages(array(
            'groupId' => $group->getId(),
            'paginate' => false
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($entities, $page, 32);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:imageEntityList.html.twig', array(
                'group' => $group,
                'entities' => $pagination
            ));
        }

        return array(
            'group' => $group,
            'entities' => $pagination
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
            'category_id'   => $category_slug ? $category->getId() : null,
            'group_id'      => $group->getId()      
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }
        
        return array('categories' => $categories, 'group' => $group, 'dates' => $pagination, 'category' => $category, 'page' => $page);
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

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('groupId' => $group->getId()));
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('slug' => $group->getSlug(), 'posts' => $pagination, 'page' => $page));
        }

        return array('group' => $group, 'members' => $members, 'request' => $req, 'biography' => $biography, 'posts' => $pagination);
    }
}
