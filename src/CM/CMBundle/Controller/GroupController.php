<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use CM\CMBundle\Entity\EntityCategory;

/**
 * @Route("/groups/{slug}", defaults={"slug": null})
 */
class GroupController extends Controller
{
    /**
     * @Route(name="group_show")
     * @Template
     */
    public function showAction($slug)
    {
        return array('name' => $slug);
    }
    
    /**
	 * @Route("/events/{page}", name = "group_events", requirements={"page" = "\d+"})
	 * @Route("/events/archive/{page}", name="group_events_archive", requirements={"page" = "\d+"}) 
	 * @Route("/events/category/{category_slug}/{page}", name="group_events_category", requirements={"page" = "\d+"})
     * @Template
     */
	public function eventsAction(Request $request, $slug, $page = 1, $category_slug = null)
	{
	    $em = $this->getDoctrine()->getManager();
		
		$publisher = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
		
		if (!$publisher) {
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
			'group_id'      => $publisher->getId()		
        ));
        
		$pagination = $this->get('knp_paginator')->paginate($events, $page, 10);
		
		if ($request->isXmlHttpRequest()) {
    		return $this->render('CMBundle:Event:objects.html.twig', array('dates' => $pagination, 'page' => $page));
		}
		
		return array('categories' => $categories, 'dates' => $pagination, 'category' => $category, 'page' => $page);
	}
}
