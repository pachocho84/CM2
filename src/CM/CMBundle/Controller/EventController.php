<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Locale;
use CM\CMBundle\Entity\EntityCategoryEnum;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\MultipleImagesType;
use CM\CMBundle\Utility\UploadHandler;

/**
 * @Route("/events")
 */
class EventController extends Controller
{
	/**
	 * @Route("/{page}", name = "event_index", requirements={"page" = "\d+"})
   * @Route("/archive/{page}", name="event_archive", requirements={"page" = "\d+"}) 
   * @Route("/category/{slug}/{page}", name="event_category", requirements={"page" = "\d+"}) 
	 * @Template
	 */
	public function indexAction(Request $request, $page = 1, $slug = null)
	{
	  $em = $this->getDoctrine()->getManager();
			
		if (!$request->isXmlHttpRequest())
		{
			$categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategoryEnum::toNum('Event'), array('locale' => $request->getLocale())); // QUESTA SOLUZIONE DI ENTITY CATEGORY ENUM FA VERAMENTE CAGARE ED È DA CAMBIARE SUBITO!!!!!!!!!
		}
		
		if ($request->get('_route') == 'event_category')
		{
			$category = $em->getRepository('CMBundle:EntityCategory')->getCategory($slug, EntityCategoryEnum::toNum('Event'), array('locale' => $request->getLocale()));
		}
		
	  $events = $em->getRepository('CMBundle:Event')->getEvents(array(
	  	'locale' => $request->getLocale(), 
	  	'archive' => $request->get('_route') == 'event_archive' ? true : null,
	  	'category' => $request->get('_route') == 'event_category' ? $category->getId() : null
	  ));
	    
		$paginator  = $this->get('knp_paginator');
		$pagination = $paginator->paginate($events, $page, 10);
			
		if ($request->isXmlHttpRequest())
		{
			return $this->render('CMBundle:Event:objects.html.twig', array('events' => $pagination));
		}
			
		return array('categories' => $categories, 'events' => $pagination, 'category' => $category);
	}
	
	/**
	 * @Route("/calendar/{year}/{month}", name = "event_calendar", requirements={"month" = "\d+", "year" = "\d+"})
	 * @Template
	 */
	public function calendarAction(Request $request)
	{
	  $em = $this->getDoctrine()->getManager();
		$categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategoryEnum::toNum('Event'), array('locale' => $request->getLocale())); // QUESTA SOLUZIONE DI ENTITY CATEGORY ENUM FA VERAMENTE CAGARE ED È DA CAMBIARE SUBITO!!!!!!!!!
		
	  $events = $em->getRepository('CMBundle:Event')->getEvents(array(
	  	'locale' => $request->getLocale(), 
	  	'archive' => $request->get('_route') == 'event_archive' ? true : null,
	  	'category' => $request->get('_route') == 'event_category' ? $category->getId() : null
	  ));
			
		return array('categories' => $categories);
	}
	
	/**
	 * @Template
	 */
	public function calendarMonthAction(Request $request)
	{
	  $em = $this->getDoctrine()->getManager();
		$categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategoryEnum::toNum('Event'), array('locale' => $request->getLocale())); // QUESTA SOLUZIONE DI ENTITY CATEGORY ENUM FA VERAMENTE CAGARE ED È DA CAMBIARE SUBITO!!!!!!!!!
		
	  $events = $em->getRepository('CMBundle:Event')->getEvents(array(
	  	'locale' => $request->getLocale(), 
	  	'archive' => $request->get('_route') == 'event_archive' ? true : null,
	  	'category' => $request->get('_route') == 'event_category' ? $category->getId() : null
	  ));
			
		return array('categories' => $categories);
	}
    
  /**
   * @Route("/{id}/{slug}", name="event_show", requirements={"id" = "\d+", "_locale" = "en|fr|it"})
   * @Template
   */
  public function showAction(Request $request, $id, $slug)
  {
    $em = $this->getDoctrine()->getManager();
    $event = $em->getRepository('CMBundle:Event')->getEvent($id, $request->getLocale());

    $images = new ArrayCollection();

    $form = $this->createForm(new MultipleImagesType(), $images, array(
    	'action' => $this->generateUrl('event_show', array(
       	'id' => $event->getId(),
      	'slug' => $event->getSlug()
      )),
    	'cascade_validation' => true
    ))->add('save', 'submit');

    $form->handleRequest($request);
    
    if ($form->isValid()) {
    	$event->addImages($images);

      $em = $this->getDoctrine()->getEntityManager();
      $em->persist($event);
      $em->flush();

      return new RedirectResponse($this->generateUrl('event_show', array('id' => $event->getId(), 'slug' => $event->getSlug())));
    }
    
    return array('event' => $event, 'form' => $form->createView());
  }
    
  /**
   * @Route("/new", name="event_new") 
   * @Route("/{id}/{slug}/edit", name="event_edit", requirements={"id" = "\d+"}) 
   * @Template
   */
  public function editAction(Request $request, $id = null, $slug = null)
  {
  	$event;
  	if ($id == null || $slug == null) {
      	$event = new Event;
		$event->addEventDate(new EventDate);
		$image = new Image;
		$image->setMain(true);
		$event->addImage($image);
		}
		else {
			$em = $this->getDoctrine()->getManager();
	    $event = $em->getRepository('CMBundle:Event')->getEvent($id, $request->getLocale());
	    // TODO: retrieve images from event
		}
	      
	  // TODO: retrieve locales from user
	
	  $form = $this->createForm(new EventType(), $event, array(
	  	'action' => $this->generateUrl('event_new'),
	    'cascade_validation' => true,
	    'locales' => array('en'/* , 'fr', 'it' */),
	    'locale' => $request->getLocale()
	  ))->add('save', 'submit');
	      
	  $form->handleRequest($request);
	      
	  if ($form->isValid()) {
	  	$em = $this->getDoctrine()->getEntityManager();
	    $em->persist($event);
	    $em->flush();
			
	    return new RedirectResponse($this->generateUrl('event_show', array('id' => $event->getId(),	'slug' => $event->getSlug())));
	  }
	
	  return array('form' => $form->createView());
  }
}
