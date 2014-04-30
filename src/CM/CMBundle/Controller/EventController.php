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
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\Sponsored;
use CM\CMBundle\Entity\Tag;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\ImageCollectionType;
use CM\CMBundle\Utility\UploadHandler;

function getAllErrors($form, &$errors = array()) {
    foreach ($form->getErrors() as $error) {
        $errors[] = $form->getName().': '.$error->getMessage();
    }
    foreach ($form->all() as $child) {
        getAllErrors($child, $errors);
    }
    return $errors;
}

/**
 * @Route("/events")
 */
class EventController extends Controller
{
    /**
     * @Route("/{page}", name = "event_index", requirements={"page" = "\d+"})
     * @Route("/archive/{page}", name="event_archive", requirements={"page" = "\d+"}) 
     * @Route("/category/{categorySlug}/{page}", name="event_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1, $categorySlug = null)
    {
        $em = $this->getDoctrine()->getManager();
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
        
        if ($categorySlug) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($categorySlug, EntityCategory::EVENT, array('locale' => $request->getLocale()));
        }
            
        $events = $em->getRepository('CMBundle:Event')->getEvents(array(
            'locale' => $request->getLocale(), 
            'archive' => $request->get('_route') == 'event_archive' ? true : null,
            'categoryId'   => $categorySlug ? $category->getId() : null
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($events, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
        }
        
        return array('categories' => $categories, 'dates' => $pagination, 'category' => $category, 'page' => $page);
    }
    
    /**
     * @Route("/calendar/{year}/{month}", name = "event_calendar", requirements={"month" = "\d+", "year" = "\d+"})
     * @Template
     */
    public function calendarAction(Request $request, $year = null, $month = null)
    {
            
        if ($request->isXmlHttpRequest()) {
            return $this->forward('CMBundle:Event:calendarMonth', array('request' => $request, 'month' => $month, 'year' => $year, 'loadMore' => $request->get('loadMore')));
        }
        
        $month = !is_null($month) ? $month : date('n');
        $year = !is_null($year) ? $year : date('Y');
        
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::EVENT, array('locale' => $request->getLocale()));
            
        return array('categories' => $categories, 'year' => $year, 'month' => $month);
    }
    
    /**
     * @Route("/calendarMonth/{year}/{month}", name = "event_calendar_month", requirements={"month" = "\d+", "year" = "\d+"})
     * @Template
     */
    public function calendarMonthAction(Request $request, $year, $month)
    {
        $request->setLocale($request->get('_locale')); // TODO: workaround for locale in subsession
        
        $em = $this->getDoctrine()->getManager();
        
        $dates = $em->getRepository('CMBundle:Event')->getEventsPerMonth($year, $month, array('locale' => $request->getLocale()));
        
        $cMonth['weekdayNames'] = array('sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'); 
        $cMonth['cMonth'] = !is_null($month) ? $month : date('n');
        $cMonth['cYear'] = !is_null($year) ? $year : date('Y');
     
        $cMonth['prev_year'] = $cMonth['cYear'];
        $cMonth['next_year'] = $cMonth['cYear'];
        $cMonth['prev_month'] = $cMonth['cMonth'] - 1;
        $cMonth['next_month'] = $cMonth['cMonth'] + 1;
         
        if ($cMonth['prev_month'] == 0) {
            $cMonth['prev_month'] = 12;
            $cMonth['prev_year'] = $cMonth['cYear'] - 1;
        }
        if ($cMonth['next_month'] == 13) {
            $cMonth['next_month'] = 1;
            $cMonth['next_year'] = $cMonth['cYear'] + 1;
        }
                    
        $cMonth['timestamp'] = mktime(12, 0, 0, $cMonth['cMonth'], 1, $cMonth['cYear']);
        $cMonth['maxday'] = date('t', strtotime($year.'-'.$month.'-01'));
        $cMonth['thismonth'] = getdate($cMonth['timestamp']);
        $cMonth['startday'] = $cMonth['thismonth']['wday'];
/*
        $cal1 = \IntlCalendar::createInstance();
        echo $cal1->getFirstDayOfWeek(); // Monday
*/
/*      $cMonth['calendar'] = \IntlCalendar::createInstance(); */

        return array('dates' => $dates, 'month' => $cMonth, 'loadMore' => $request->get('loadMore'));
    }
    
    /**
     * @Route("/dates/next/{object}/{objectId}", name="event_next_dates", requirements={"id" = "\d+"})
     * @Template
     */
    public function nextDatesAction(Request $request, $object, $objectId)
    {
        $em = $this->getDoctrine()->getManager();

        $dates = $em->getRepository('CMBundle:Event')->getNextDates(array(
            $object.'Id' => $objectId,
            'locale' => $request->getLocale(),
            'paginate' => false,
            'exclude' => $request->get('exclude'),
            'limit' => $request->get('limit')
        ));

        if (count($dates) == 0) {
            return new Response;
        }

        return array(
            'dates' => $dates,
            'link' => $this->generateUrl($dates[0]->getEvent()->getPost()->getPublisherType().'_events', array('slug' => $dates[0]->getEvent()->getPost()->getPublisher()->getSlug())),
            'count' => $em->getRepository('CMBundle:Event')->countNextDates(array($object.'Id' => $objectId))
        );
    }
    
    /**
     * @Route("/dates/{id}", name="event_dates", requirements={"id" = "\d+"})
     * @Template
     */
    public function datesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        return array('dates' => $em->getRepository('CMBundle:Event')->getDatesPerEvent($id));
    }
    
    /**
     * @Route("/new", name="event_new")
     * @Route("/{id}/{slug}/edit", name="event_edit", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        
        if ($id == null || $slug == null) {
            $event = new Event;

            $event->addUser(
                $user,
                true, // admin
                EntityUser::STATUS_ACTIVE,
                true // notifications
            );

            $image = new Image;
            $image->setMain(true)
                ->setUser($user);
            $event->setImage($image);

            // $event->addMultimedia(new Multimedia);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);
            $event->setPost($post);
        } else {
            $event = $em->getRepository('CMBundle:Event')->getEvent($id, array(
                'slug' => $slug,
                'locale' => $request->getLocale(),
                'protagonists' => true,
                'tags' => true
            ));

            if (!$this->get('cm.user_authentication')->canManage($event)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }

        $event->addEventDate(new EventDate);
 
        $form = $this->createForm(new EventType, $event, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $user->getRoles(),
            'tags' => $em->getRepository('CMBundle:Tag')->getTags(array('type' => Tag::TYPE_ENTITY_USER, 'locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ))->add('save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($event);
            $em->flush();

            return new RedirectResponse($this->generateUrl('event_show', array('id' => $event->getId(), 'slug' => $event->getSlug())));
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $event,
            'newEntry' => ($request->get('_route') == 'event_new'),
            'joinEntityType' => 'joinEvent'
        );
    }

    /**
     * @Route("/eventDelete/{id}", name="event_delete", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('CMBundle:Event')->findOneById($id);

        if (!$this->get('cm.user_authentication')->canManage($event)) {
              throw new HttpException(401, 'Unauthorized access.');
        }

        $em->remove($event);
        $em->flush();

        die;

        return new RedirectResponse($this->generateUrl('event_index'));
    }
    
    /**
     * @Route("/sponsored/{limit}", name="event_sponsored", requirements={"limit" = "\d+"})
     * @Template
     */
    public function sponsoredAction(Request $request, $limit = 3)
    {
        $em = $this->getDoctrine()->getManager();
        $sponsored = $em->getRepository('CMBundle:Event')->getSponsored(array('limit' => $limit, 'locale' => $request->getLocale()));
        
        return array('sponsoredEvents' => $sponsored);
    }

    /**
     * @Route("/{id}/{slug}", name="event_show", requirements={"id" = "\d+", "_locale" = "en|fr|it"})
     * @Template
     */
    public function showAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
            
        if ($request->isXmlHttpRequest()) {
            $date = $em->getRepository('CMBundle:Event')->getDate($id, array('slug' => $slug, 'locale' => $request->getLocale()));
            return $this->render('CMBundle:Event:object.html.twig', array('date' => $date));
        }
        
        $event = $em->getRepository('CMBundle:Event')->getEvent($id, array(
            'slug' => $slug,
            'protagonist' => is_null($this->getUser()) ?: $this->getUser()->getId(),
            'locale' => $request->getLocale()
        ));

        return array(
            'event' => $event
        );
    }
}
