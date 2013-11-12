<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
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
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Form\EventType;

class UserController extends Controller
{
    /**
     * @Route("/typeaheadHint", name="user_typeahead_hint")
     */
    public function typeaheadHintAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $exclude = $request->query->get('exclude') ? explode(',', $request->query->get('exclude')) : array();
        $exclude[] = $this->getUser()->getId();
        $users = $em->getRepository('CMBundle:User')->getFromAutocomplete($request->query->get('query'), $exclude);

        $results = array();
        foreach($users as $user)
        {
            if ($user['img'] == '' || $user['img'] == null) {
                $user['img'] = '/bundles/cm/uploads/images/50/default.jpg';
            } else {
                $user['img'] = '/bundles/cm/uploads/images/350/'.$user['img'];
            }
            $user['fullname'] = $user['firstName'].' '.$user['lastName'];
            $results[] = $user;

            
            // $view['view'] = $this->renderView('CMBundle:User:typeaheadHint.html.twig', array('user' => $user));
            // $results[] = $view;
        }

        return new JsonResponse($results);
    }

    /**
     * @Template
     */
    public function menuAction(Request $request)
    {
        $newRequests = $this->get('cm.request_center')->getNewRequestsNumber($this->getUser()->getId());
        $newNotifications = $this->get('cm.notification_center')->getNewNotificationsNumber($this->getUser()->getId());

        $inRequest = substr($request->get('realRoute'), 1, 7) == 'request';
        $inNotification = substr($request->get('realRoute'), 1, 12) == 'notification';

        return array(
            'newRequests' => $newRequests,
            'newNotifications' => $newNotifications,
            'inRequestPage' => $inRequest,
            'inNotificationPage' => $inNotification
        );
    }

    /**
     * @Route("/requests/{page}/{perPage}", name="user_requests", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER") 
     * @Template
     */
    public function requestsAction(Request $request, $page = 1, $perPage = 6)
    {
        $em = $this->getDoctrine()->getManager();

        $requests = $em->getRepository('CMBundle:Request')->getRequests($this->getUser()->getId());
        $pagination = $this->get('knp_paginator')->paginate($requests, $page, $perPage);

        $this->get('cm.request_center')->seeRequests($this->getUser()->getId());

        if ($request->isXmlHttpRequest() && !$request->get('outgoing')) {
            return $this->render('CMBundle:User:requestList.html.twig', array('requests' => $pagination));
        }

        $requestsOutgoing = $em->getRepository('CMBundle:Request')->getRequests($this->getUser()->getId(), 'outgoing');
        $paginationOutgoing = $this->get('knp_paginator')->paginate($requestsOutgoing, $page, $perPage);

        if ($request->isXmlHttpRequest() && $request->get('outgoing')) {
            return $this->render('CMBundle:User:requestOutgoingList.html.twig', array('requests' => $paginationOutgoing));
        }

        return array('requests' => $pagination, 'requestsOutgoing' => $paginationOutgoing);
    }

    /**
     * @Route("/requestAdd/{object}/{objectId}", name="user_request_add", requirements={"objectId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function requestAddAction($object, $objectId)
    {
        $object = $this->get('cm.helper')->fullClassName($object);

        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Event':
                $event = $em->getRepository('CMBundle:Event')->findOneById($objectId);
                $event->addUser(
                    $this->getUser(),
                    false, // admin
                    EntityUser::STATUS_REQUESTED,
                    true // notifications
                );
                $em->persist($event);
                break;
        }

        $em->flush();

        return new Response;
    }

    /**
     * @Route("/requestUpdate/{object}/{objectId}/{choice}", name="user_request_update", requirements={"objectId"="\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function requestUpdateAction($object, $objectId, $choice)
    {
        $object = $this->get('cm.helper')->fullClassName($object);

        if ($choice == 'accept') {
            $this->get('cm.request_center')->acceptRequest($this->getUser()->getId(), $object, $objectId);
        } elseif ($choice == 'refuse') {
            $this->get('cm.request_center')->refuseRequest($this->getUser()->getId(), $object, $objectId);
        }

        return new Response;
    }
    
    /**
     * @Route("/requestDelete/{object}/{objectId}", name="user_request_delete", requirements={"objectId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function requestDeleteAction($object, $objectId)
    {
        $object = $this->get('cm.helper')->fullClassName($object);
        
        $em = $this->getDoctrine()->getManager();

        $this->get('cm.request_center')->removeRequest($this->getUser()->getId(), $object, $objectId);

        return new Response;
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
     * @Route("/{slug}", name="user_show")
     * @Template
     */
    public function showAction($slug)
    {
        return array('username' => $slug);
    }
    
    /**
	 * @Route("/{slug}/events/{page}", name="user_events", requirements={"page" = "\d+"})
	 * @Route("/{slug}/events/archive/{page}", name="user_events_archive", requirements={"page" = "\d+"}) 
	 * @Route("/{slug}/events/category/{category_slug}/{page}", name="user_events_category", requirements={"page" = "\d+"})
     * @Template
     */
	public function eventsAction(Request $request, $slug, $page = 1, $category_slug = null)
	{
	    $em = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
		
		if (!$user) {
    		throw new NotFoundHttpException('User not found.');
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
			'category_id'   => $category_slug ? $category->getId() : null,
			'user_id'       => $user->getId()		
        ));
        
		$pagination = $this->get('knp_paginator')->paginate($events, $page, 10);
		
		if ($request->isXmlHttpRequest()) {
    		return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
		}
		
		return array('categories' => $categories, 'user' => $user, 'dates' => $pagination, 'category' => $category, 'page' => $page);
	}
}
