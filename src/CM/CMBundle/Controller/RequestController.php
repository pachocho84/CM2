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
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\GroupUser;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\UserImageType;

class RequestController extends Controller
{
    /**
     * @Route("/requests/{page}/{perPage}", name="request_index", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER") 
     * @Template
     */
    public function indexAction(Request $request, $page = 1, $perPage = 6)
    {
        $em = $this->getDoctrine()->getManager();

        $requests = $em->getRepository('CMBundle:Request')->getRequests($this->getUser()->getId());
        $pagination = $this->get('knp_paginator')->paginate($requests, $page, $perPage);

        $this->get('cm.request_center')->seeRequests($this->getUser()->getId());

        if ($request->isXmlHttpRequest() && !$request->get('outgoing')) {
            return $this->render('CMBundle:Request:requestList.html.twig', array('requests' => $pagination));
        }

        $requestsOutgoing = $em->getRepository('CMBundle:Request')->getRequests($this->getUser()->getId(), 'outgoing');
        $paginationOutgoing = $this->get('knp_paginator')->paginate($requestsOutgoing, $page, $perPage);

        
        if ($request->isXmlHttpRequest() && $request->get('outgoing')) {
            return $this->render('CMBundle:Request:requestOutgoingList.html.twig', array('requests' => $paginationOutgoing));
        }

        return array('requests' => $pagination, 'requestsOutgoing' => $paginationOutgoing);
    }

    /**
     * @Route("/requestAdd/{object}/{objectId}", name="request_add", requirements={"objectId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addAction(Request $request, $object, $objectId)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Event':
                if (!empty($em->getRepository('CMBundle:EntityUser')->findOneBy(array('user' => $this->getUser(), 'entityId' => $objectId)))) {
                    throw new HttpException(403, $this->get('translator')->trans('Request already sent.', array(), 'http-errors'));
                }
                $event = $em->getRepository('CMBundle:Event')->findOneById($objectId);
                $event->addUser(
                    $this->getUser(),
                    false, // admin
                    EntityUser::STATUS_REQUESTED,
                    true // notifications
                );
                $em->persist($event);
                $post = $event->getPost();
                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('post' => $post));
                break;
            case 'Group':
                if (!empty($em->getRepository('CMBundle:GroupUser')->findOneBy(array('user' => $this->getUser(), 'groupId' => $objectId)))) {
                    throw new HttpException(403, $this->get('translator')->trans('Request already sent.', array(), 'http-errors'));
                }
                $group = $em->getRepository('CMBundle:Group')->findOneById($objectId);
                $group->addUser(
                    $this->getUser(),
                    false, // admin
                    GroupUser::STATUS_REQUESTED,
                    true // notifications
                );
                $em->persist($group);
                $post = $group->getPost();
                $response = $this->renderView('CMBundle:GroupUser:requestAdd.html.twig', array('post' => $post));
                break;
        }

        $em->flush(); // TODO: why so many sql queries?

        return new Response($response);
    }

    /**
     * @Route("/requestUpdate/{object}/{objectId}/{choice}/{slug}", name="request_update", requirements={"objectId"="\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function updateAction(Request $request, $object, $objectId, $choice, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Event':
                if (!is_null($slug)) {
                    $event = $em->getRepository('CMBundle:Event')->findOneById($objectId);

                    if (!$this->get('cm.user_authentication')->canManage($event)) {
                        throw new HttpException(401, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }

                    $this->get('cm.request_center')->removeRequest($user->getId(), array('entityId' => $objectId, 'exclude' => $this->getUser()->getId()), 'sent');
                } else {
                    $user = $this->getUser();
                }

                if ($choice == 'accept') {
                    $this->get('cm.request_center')->acceptRequest($user->getId(), array('entityId' => $objectId));
                } elseif ($choice == 'refuse') {
                    $this->get('cm.request_center')->refuseRequest($user->getId(), array('entityId' => $objectId));
                }

                $post = $em->getRepository('CMBundle:Entity')->getCreationPost($objectId, $this->get('cm.helper')->fullClassName($object));
                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('post' => $post));
                break;
            case 'Group':
                if (!is_null($slug)) {
                    $group = $em->getRepository('CMBundle:Group')->findOneById($objectId);

                    if (!$this->get('cm.user_authentication')->canManage($group)) {
                        throw new HttpException(401, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }

                    $this->get('cm.request_center')->removeRequest($user->getId(), array('groupId' => $objectId, 'exclude' => $this->getUser()->getId()), 'sent');
                } else {
                    $user = $this->getUser();
                }
                
                if ($choice == 'accept') {
                    $this->get('cm.request_center')->acceptRequest($user->getId(), array('groupId' => $objectId));
                } elseif ($choice == 'refuse') {
                    $this->get('cm.request_center')->refuseRequest($user->getId(), array('groupId' => $objectId));
                }

                $post = $em->getRepository('CMBundle:Group')->getCreationPost($objectId, $this->get('cm.helper')->fullClassName($object));
                $response = $this->renderView('CMBundle:GroupUser:requestAdd.html.twig', array('post' => $post));
                break;
        }

        if ($this->get('cm.request_center')->flushNeeded()) {
            $em->flush();
        }

        return new Response($response);
    }
    
    /**
     * @Route("/requestDelete/{object}/{objectId}/{slug}", name="request_delete", requirements={"objectId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $object, $objectId, $slug = null)
    {        
        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Event':
                if (!is_null($slug)) {
                    $event = $em->getRepository('CMBundle:Event')->findOneById($objectId);

                    if (!$this->get('cm.user_authentication')->canManage($event)) {
                        throw new HttpException(401, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }

                    $direction = "received";
                } else {
                    $user = $this->getUser();

                    $direction = "sent";
                }

                $this->get('cm.request_center')->removeRequest($user->getId(), array('entityId' => $objectId), $direction);
                $em->getRepository('CMBundle:EntityUser')->delete($userId, $objectId);

                $post = $em->getRepository('CMBundle:Entity')->getCreationPost($objectId, $this->get('cm.helper')->fullClassName($object));
                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('post' => $post));
                break;
            case 'Group':
                if (!is_null($slug)) {
                    $group = $em->getRepository('CMBundle:Group')->findOneById($objectId);

                    if (!$this->get('cm.user_authentication')->canManage($group)) {
                        throw new HttpException(401, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }

                    $direction = "received";
                } else {
                    $user = $this->getUser();

                    $direction = "sent";
                }

                $this->get('cm.request_center')->removeRequest($user->getId(), array('groupId' => $objectId), $direction);
                $em->getRepository('CMBundle:GroupUser')->delete($user->getId(), $objectId);

                $post = $em->getRepository('CMBundle:Group')->getCreationPost($objectId, $this->get('cm.helper')->fullClassName($object));
                $response = $this->renderView('CMBundle:GroupUser:requestAdd.html.twig', array('post' => $post));
                break;
        }

        return new Response($response);
    }
}
