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
use CM\CMBundle\Entity\PageUser;
use CM\CMBundle\Entity\Relation;
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

/*
        $requestsOutgoing = $em->getRepository('CMBundle:Request')->getRequests($this->getUser()->getId(), 'outgoing');
        $paginationOutgoing = $this->get('knp_paginator')->paginate($requestsOutgoing, $page, $perPage);

        
        if ($request->isXmlHttpRequest() && $request->get('outgoing')) {
            return $this->render('CMBundle:Request:requestOutgoingList.html.twig', array('requests' => $paginationOutgoing));
        }
*/

        return array('requests' => $pagination, 'requestsOutgoing' => $paginationOutgoing);
    }

    /**
     * @Route("/requestAdd/{object}/{objectId}/{userId}", name="request_add", requirements={"objectId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addAction(Request $request, $object, $objectId = null, $userId = null)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Event':
                if (count($em->getRepository('CMBundle:EntityUser')->findBy(array('entityId' => $objectId, 'userId' => $userId))) > 0) {
                     throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                }

                $event = $em->getRepository('CMBundle:Event')->findOneById($objectId);
                if ($userId != $this->getUser()->getId()) {
                    if (!$this->get('cm.user_authentication')->canManage($event)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneById($userId);
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }
                    $status = EntityUser::STATUS_PENDING;
                } else {
                    $user = $this->getUser();
                    $status = EntityUser::STATUS_REQUESTED;
                }

                $event->addUser(
                    $user,
                    false, // admin
                    $status
                );
                $em->persist($event);

                $em->flush();

                $request = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($user->getId(), 'any', array('entityId' => $event->getId()));

                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $request->getEntity(), 'request' => $request));
                break;
            case 'Disc':
                if (count($em->getRepository('CMBundle:EntityUser')->findBy(array('entityId' => $objectId, 'userId' => $userId))) > 0) {
                     throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                }

                $disc = $em->getRepository('CMBundle:Disc')->findOneById($objectId);
                if ($userId != $this->getUser()->getId()) {
                    if (!$this->get('cm.user_authentication')->canManage($disc)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneById($userId);
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }
                    $status = EntityUser::STATUS_PENDING;
                } else {
                    $user = $this->getUser();
                    $status = EntityUser::STATUS_REQUESTED;
                }

                $disc->addUser(
                    $user,
                    false, // admin
                    $status
                );
                $em->persist($disc);

                $em->flush();

                $request = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($user->getId(), 'any', array('entityId' => $disc->getId()));

                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $request->getEntity(), 'request' => $request));
                break;
            case 'Article':
                if (count($em->getRepository('CMBundle:EntityUser')->findBy(array('entityId' => $objectId, 'userId' => $userId))) > 0) {
                     throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                }

                $article = $em->getRepository('CMBundle:Article')->findOneById($objectId);
                if ($userId != $this->getUser()->getId()) {
                    if (!$this->get('cm.user_authentication')->canManage($article)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneById($userId);
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }
                    $status = EntityUser::STATUS_PENDING;
                } else {
                    $user = $this->getUser();
                    $status = EntityUser::STATUS_REQUESTED;
                }

                $article->addUser(
                    $user,
                    false, // admin
                    $status
                );
                $em->persist($article);

                $em->flush();

                $request = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($user->getId(), 'any', array('entityId' => $article->getId()));

                $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $request->getEntity(), 'request' => $request));
                break;
            case 'Page':
                if (count($em->getRepository('CMBundle:PageUser')->findBy(array('pageId' => $objectId, 'userId' => $userId))) > 0) {
                    throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                }

                $page = $em->getRepository('CMBundle:Page')->findOneById($objectId);
                if ($userId != $this->getUser()->getId()) {
                    if (!$this->get('cm.user_authentication')->isAdminOf($page)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }

                    $user = $em->getRepository('CMBundle:User')->findOneById($userId);
                    if (!$user) {
                        throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
                    }
                    $status = PageUser::STATUS_PENDING;
                } else {
                    throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                }

                $page->addUser(
                    $user,
                    false, // admin
                    $status
                );
                $em->persist($page);

                $em->flush();

                $request = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($user->getId(), 'any', array('pageId' => $page->getId()));

                $response = $this->renderView('CMBundle:PageUser:requestAdd.html.twig', array('page' => $request->getPage(), 'request' => $request));
                break;
        }

        return new Response($response);
    }

    /**
     * @Route("/requestUpdate/{id}/{choice}", name="request_update", requirements={"id"="\d+", "choice"="accept|refuse"})
     * @Route("/requestUpdateByObject/{object}/{id}/{choice}", name="request_update_object", requirements={"id"="\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function updateAction($id, $choice, $object = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($object)) {
            $request = $em->getRepository('CMBundle:Request')->getRequest($id);
        } else {
            $request = $em->getRepository('CMBundle:Request')->findOneBy(array(
                'userId' => $this->getUser()->getId(),
                'object' => $this->get('cm.helper')->fullClassName(ucfirst($object)),
                'objectId' => $id
            ));
        }

        if (in_array($request->getStatus(), array(\CM\CMBundle\Entity\Request::STATUS_ACCEPTED, \CM\CMBundle\Entity\Request::STATUS_REFUSED))) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if (!is_null($request->getEntityId())) {
            if ($this->get('cm.user_authentication')->isAdminOf($request->getEntity())) {
                $em->getRepository('CMBundle:Request')->delete($this->getUser()->getId(), array('fromUserId' => $request->getFromUserId(), 'entityId' => $request->getEntityId(), 'exclude' => true));

                $userId = $request->getFromUserId();
            } else {
                $userId = $this->getUser()->getId();
            }

            $entityUser = $em->getRepository('CMBundle:EntityUser')->findOneBy(array('userId' => $userId, 'entityId' => $request->getEntityId()));
            if ($choice == 'accept') {
                $entityUser->setStatus(EntityUser::STATUS_ACTIVE);
            } elseif ($choice == 'refuse') {
                $entityUser->setStatus($entityUser->getStatus() == EntityUser::STATUS_PENDING ? EntityUser::STATUS_REFUSED_ENTITY_USER : EntityUser::STATUS_REFUSED_ADMIN);
            }
            $em->persist($entityUser);

            $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $request->getEntity(), 'request' => $request));
        }elseif (!is_null($request->getPageId())) {
            $userId = $this->getUser()->getId();

            $pageUser = $em->getRepository('CMBundle:PageUser')->findOneBy(array('userId' => $userId, 'pageId' => $request->getPageId()));
            if ($choice == 'accept') {
                $pageUser->setStatus(PageUser::STATUS_ACTIVE);
            } elseif ($choice == 'refuse') {
                $pageUser->setStatus($pageUser->getStatus() == PageUser::STATUS_PENDING ? PageUser::STATUS_REFUSED_PAGE_USER : PageUser::STATUS_REFUSED_ADMIN);
            }
            $em->persist($pageUser);

            $response = $this->renderView('CMBundle:PageUser:requestAdd.html.twig', array('page' => $request->getPage(), 'request' => $request));
        }

        $em->remove($request);
        $em->flush();

        return new Response($response);
    }
    
    /**
     * @Route("/requestDelete/{id}", name="request_delete", requirements={"id"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction($id, $object = null)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $em->getRepository('CMBundle:Request')->getRequest($id);

        if ($request->getStatus() == \CM\CMBundle\Entity\Request::STATUS_REFUSED) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if (!is_null($request->getEntityId())) {
            if ($this->get('cm.user_authentication')->isAdminOf($request->getEntity())) {
                $userId = $request->getUserId();
                $em->getRepository('CMBundle:Request')->delete($userId, array('fromUserId' => $this->getUser()->getId(), 'entityId' => $request->getEntityId()));
            } else {
                $userId = $this->getUser()->getId();
                $em->getRepository('CMBundle:Request')->delete(null, array('fromUserId' => $this->getUser()->getId(), 'entityId' => $request->getEntityId()));
            }

            $em->getRepository('CMBundle:EntityUser')->delete($userId, $request->getEntityId());

            $response = $this->renderView('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $request->getEntity(), 'request' => null));
        } elseif (!is_null($request->getPageId())) {
            if ($this->get('cm.user_authentication')->isAdminOf($request->getPage())) {
                $userId = $request->getUserId();
                $em->getRepository('CMBundle:Request')->delete($userId, array('fromUserId' => $this->getUser()->getId(), 'pageId' => $request->getPageId()));
            }

            $em->getRepository('CMBundle:PageUser')->delete($userId, $request->getPageId());
            

            $response = $this->renderView('CMBundle:PageUser:requestAdd.html.twig', array('page' => $request->getPage(), 'request' => null));
        } elseif ($this->get('cm.helper')->className($request->getObject()) == 'Relation') {
            $user = $request->getFromUser();

            $relation = $em->getRepository('CMBundle:Relation')->findOneById($request->getObjectId());

            if (!$relation) {
                throw new NotFoundHttpException($this->get('translator')->trans('Relation not found.', array(), 'http-errors'));
            }

            $inverse = $em->getRepository('CMBundle:Relation')->getInverse($relation->getRelationType(), $relation->getUserId(), $relation->getFromUserId());

            $em->remove($relation);
            $em->remove($inverse);
            $em->remove($request);

            $em->flush();

            if ($user->getId() == $this->getUser()->getId()) {
                $user = $request->getUser();
            }
            return $this->forward('CMBundle:Relation:button', array('user' => $user));
        }

        return new Response($response);
    }
}
