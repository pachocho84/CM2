<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Disc;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\DiscTrack;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\DiscType;
use CM\CMBundle\Form\DiscTrackType;
use CM\CMBundle\Form\ImageCollectionType;
use CM\CMBundle\Utility\UploadHandler;

/**
 * @Route("/discs")
 */
class DiscController extends Controller
{
    /**
     * @Route("/{page}", name = "disc_index", requirements={"page" = "\d+"})
     * @Route("/category/{categorySlug}/{page}", name="disc_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1, $categorySlug = null)
    {
        $em = $this->getDoctrine()->getManager();
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::DISC, array('locale' => $request->getLocale()));
        }
        
        if (!is_null($categorySlug)) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($categorySlug, EntityCategory::DISC, array('locale' => $request->getLocale()));
        }
            
        $discs = $em->getRepository('CMBundle:Disc')->getDiscs(array(
            'locale' => $request->getLocale(),
            'categoryId' => $categorySlug ? $category->getId() : null
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($discs, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Disc:objects.html.twig', array('discs' => $pagination));
        }
        
        return array('categories' => $categories, 'discs' => $pagination, 'category' => $category);
    }
    
    /**
     * @Route("/tracks/{id}", name="disc_tracks", requirements={"id" = "\d+"})
     * @Template
     */
    public function tracksAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        return array('tracks' => $em->getRepository('CMBundle:Disc')->getTracksPerDisc($id));
    }

    /**
     * @Route("/new", name="disc_new") 
     * @Route("/{id}/{slug}/edit", name="disc_edit", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        
        if ($id == null || $slug == null) {
            
            $disc = new Disc;

            $disc->addUser(
                $user,
                true, // admin
                EntityUser::STATUS_ACTIVE,
                true // notifications
            );

            $image = new Image;
            $image->setMain(true)
                ->setUser($user);
            $disc->addImage($image);

            $disc->addDiscTrack(new DiscTrack);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);

            $disc->addPost($post);
        } else {
            $disc = $em->getRepository('CMBundle:Disc')->getDisc($id, array('locale' => $request->getLocale(), 'protagonists' => true));
            if (!$this->get('cm.user_authentication')->canManage($disc)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
            // TODO: retrieve images from disc
        }

        $oldEntityUsers = array();
        foreach ($disc->getEntityUsers() as $oldEntityUser) {
            $oldEntityUsers[] = $oldEntityUser;
        }

        $oldDiscTracks = array();
        foreach ($disc->getDiscTracks() as $oldDiscTrack) {
            $oldDiscTracks[] = $oldDiscTrack;
        }
        
        // TODO: retrieve locales from user

        if ($request->get('_route') == 'disc_edit') {
            $formRoute = 'disc_edit';
            $formRouteArgs = array('id' => $disc->getId(), 'slug' => $disc->getSlug());
        } else {
            $formRoute = 'disc_new';
            $formRouteArgs = array();
        }
 
        $form = $this->createForm(new DiscType, $disc, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $user->getRoles(),
            'user_tags' => $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            foreach ($disc->getDiscTracks() as $discDate) {
                foreach ($oldDiscTracks as $key => $toDel) {
                    if ($toDel->getId() === $discDate->getId()) {
                        unset($oldDiscTracks[$key]);
                    }
                }
            }
    
            // remove the relationship between the tag and the Task
            foreach ($oldDiscTracks as $discDate) {
                // remove the Task from the Tag
                $disc->removeDiscTrack($discDate);
    
                // if it were a ManyToOne relationship, remove the relationship like this
                // $tag->setTask(null);
    
                // if you wanted to delete the Tag entirely, you can also do that
                $em->remove($discDate);
            }

            foreach ($disc->getEntityUsers() as $entityUser) {
                foreach ($oldEntityUsers as $key => $toDel) {
                    if ($toDel->getId() === $entityUser->getId()) {
                        unset($oldEntityUsers[$key]);
                    }
                }
            }

            // remove the relationship between the tag and the Task
            foreach ($oldEntityUsers as $entityUser) {
                // remove the Task from the Tag
                $disc->removeEntityUser($entityUser);

                $entityUser->setEntity(null);
                $entityUser->setUser(null);
    
                $em->remove($entityUser);
            }

            $em->persist($disc);

            $em->flush();

            // foreach ($disc->getEntityUsers() as $entityUser) {
            //     echo $entityUser->getUser().' -> i: '.count($entityUser->getUser()->getRequestsIncoming()).', o: '.'<br/>';
            // }
            // die;

            return new RedirectResponse($this->generateUrl('disc_show', array('id' => $disc->getId(), 'slug' => $disc->getSlug())));
        }

        $users = array();
        foreach ($disc->getEntityUsers() as $entityUser) {
            $users[] = $entityUser->getUser();
        }

        $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::DISC, array('locale' => $request->getLocale()));
        
        return array(
            'form' => $form->createView(),
            'entity' => $disc,
            'categories' => $categories,
            'newEntry' => ($formRoute == 'disc_new'),
            'joinEntityType' => 'joinDisc'
        );
    }
    
    /**
     * @Route("/lasts/{object}/{objectId}", name="disc_latests", requirements={"id" = "\d+"})
     * @Template
     */
    public function latestsAction(Request $request, $object, $objectId)
    {
        $em = $this->getDoctrine()->getManager();

        $discs = $em->getRepository('CMBundle:Disc')->getLatests(array(
            $object.'Id' => $objectId,
            'paginate' => false,
            'exclude' => $request->get('exclude'),
            'limit' => $request->get('limit')
        ));

        if (count($discs) == 0) {
            return new Response;
        }

        return array(
            'discs' => $discs,
            'link' => $this->generateUrl($discs[0]->getPost()->getPublisherType().'_discs', array('slug' => $discs[0]->getPost()->getPublisher()->getSlug())),
            'count' => $em->getRepository('CMBundle:Disc')->countLatests(array($object.'Id' => $objectId))
        );
    }
    
    /**
     * @Route("/sponsored/{limit}", name="disc_sponsored", requirements={"limit" = "\d+"})
     * @Template
     */
    public function sponsoredAction(Request $request, $limit = 3)
    {
        $em = $this->getDoctrine()->getManager();
        $sponsored = $em->getRepository('CMBundle:Disc')->getSponsored(array('limit' => $limit, 'locale' => $request->getLocale()));
        
        return array('sponsoredDiscs' => $sponsored);
    }

    /**
     * @Route("/discDelete/{id}", name="disc_delete", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $disc = $em->getRepository('CMBundle:Disc')->findOneById($id);

        if (!$this->get('cm.user_authentication')->canManage($disc)) {
              throw new HttpException(401, 'Unauthorized access.');
        }

        $em->remove($disc);
        $em->flush();

        return new JsonResponse(array('title' => $disc->getTitle()));
    }

    /**
     * @Route("/{id}/{slug}", name="disc_show", requirements={"id" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $disc = $em->getRepository('CMBundle:Disc')->getDisc($id, array('locale' => $request->getLocale(), 'protagonists' => true));
        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Disc:object.html.twig', array('disc' => $disc, 'tags' => $tags));
        }

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $req = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($this->getUser()->getId(), 'any', array('entityId' => $disc->getId()));
        }
        
        return array('disc' => $disc, 'request' => $req, 'tags' => $tags);
    }
}
