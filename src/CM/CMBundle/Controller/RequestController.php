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
     * @Route("/requestAdd/{object}/{objectId}/{userId}", name="request_add", requirements={"objectId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addAction(Request $request, $object, $objectId, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Event':
                $event = $em->getRepository('CMBundle:Event')->findOneById($objectId);
                if ($userId != $this->getUser()->getId()) {

                    if (!$this->get('cm.user_authentication')->canManage($event)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneById($userId);
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }
                } else {
                    $user = $this->getUser();
                }

                if (!empty($em->getRepository('CMBundle:EntityUser')->findOneBy(array('userId' => $user->getId(), 'entityId' => $objectId)))) {
                    throw new HttpException(403, $this->get('translator')->trans('Request already sent.', array(), 'http-errors'));
                }

                $event->addUser(
                    $user,
                    false, // admin
                    EntityUser::STATUS_REQUESTED,
                    true // notifications
                );
                $em->persist($event);
                $post = $event->getPost();
                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('post' => $post));
                break;
            case 'Group':
                $group = $em->getRepository('CMBundle:Group')->findOneById($objectId);
                if ($userId != $this->getUser()->getId()) {
                    if (!$this->get('cm.user_authentication')->canManage($group)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneById($userId);
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }
                    $status = GroupUser::STATUS_PENDING;
                } else {
                    $user = $this->getUser();
                    $status = GroupUser::STATUS_REQUESTED;
                }

                $group->addUser(
                    $user,
                    false, // admin
                    $status,
                    true // notifications
                );
                $em->persist($group);

                $em->flush();

                $request = $em->getRepository('CMBundle:Request')->getRequestWith($this->getUser()->getId(), 'outgoing', array('groupId' => $group->getId()));

                $response = $this->renderView('CMBundle:GroupUser:requestAdd.html.twig', array('request' => $request));
                break;
        }

        return new Response($response);
    }

    /**
     * @Route("/requestUpdate/{id}/{choice}", name="request_update", requirements={"id"="\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function updateAction($id, $choice)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $em->getRepository('CMBundle:Request')->findOneById($id);

        if (in_array($request->getStatus(), array(\CM\CMBundle\Entity\Request::STATUS_ACCEPTED, \CM\CMBundle\Entity\Request::STATUS_REFUSED))) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if (!is_null($request->getEntityId())) {

        } elseif (!is_null($request->getGroupId())) {
            if ($this->get('cm.user_authentication')->isAdminOf($request->getGroup())) {
                $em->getRepository('CMBundle:Request')->delete($this->getUser()->getId(), array('fromUserId' => $request->getFromUserId(), 'groupId' => $request->getGroupId(), 'exclude' => true));

                $userId = $request->getFromUserId();
            } else {
                $userId = $this->getUser()->getId();
            }

            if ($choice == 'accept') {
                $groupUser = $em->getRepository('CMBundle:GroupUser')->findOneBy(array('userId' => $userId, 'groupId' => $request->getGroupId()));
                $groupUser->setStatus(GroupUser::STATUS_ACTIVE);
                
                $request->setStatus(\CM\CMBundle\Entity\Request::STATUS_ACCEPTED);
            } elseif ($choice == 'refuse') {
                $groupUser = $em->getRepository('CMBundle:GroupUser')->findOneBy(array('userId' => $userId, 'groupId' => $request->getGroupId()));
                $groupUser->setStatus($groupUser->getStatus() == GroupUser::STATUS_PENDING ? GroupUser::STATUS_REFUSED_GROUP_USER : GroupUser::STATUS_REFUSED_ADMIN);

                $request->setStatus(\CM\CMBundle\Entity\Request::STATUS_REFUSED);
            }

            $response = $this->renderView('CMBundle:GroupUser:requestAdd.html.twig', array('request' => $request));
        }

        $em->persist($groupUser);
        $em->persist($request);
        $em->flush();

        return new Response($response);
    }
    
    /**
     * @Route("/requestDelete/{id}", name="request_delete", requirements={"id"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction($id)
    {        
        $em = $this->getDoctrine()->getManager();

        $request = $em->getRepository('CMBundle:Request')->findOneById($id);

        if (in_array($request->getStatus(), array(\CM\CMBundle\Entity\Request::STATUS_ACCEPTED, \CM\CMBundle\Entity\Request::STATUS_REFUSED))) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if (!is_null($request->getEntityId())) {

        } elseif (!is_null($request->getGroupId())) {
            if ($this->get('cm.user_authentication')->isAdminOf($request->getGroup())) {
                $userId = $request->getUserId();
                $em->getRepository('CMBundle:Request')->delete($userId, array('fromUserId' => $this->getUser()->getId(), 'groupId' => $request->getGroupId()));
            } else {
                $userId = $this->getUser()->getId();
                $em->getRepository('CMBundle:Request')->delete(null, array('fromUserId' => $this->getUser()->getId(), 'groupId' => $request->getGroupId()));
            }

            $em->getRepository('CMBundle:GroupUser')->delete($userId, $request->getGroupId());
            

            $response = $this->renderView('CMBundle:GroupUser:requestAdd.html.twig', array('group' => $request->getGroup(), 'request' => null));
        }

        if ($this->get('cm.request_center')->flushNeeded()) {
            $em->flush();
        }

        return new Response($response);
    }
}