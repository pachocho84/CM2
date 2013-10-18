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
use CM\CMBundle\Entity\EntityCategory;

class UserController extends Controller
{
    /**
     * @Route(name="user_show")
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

    /**
     * @Route("/typeaheadHint", name="user_typeahead_hint")
     */
    public function typeaheadHintAction(Request $request)
    {
        return new JsonResponse(array('test', 'prova'));

        $exclusion = explode(',', $request->getParameter('exclusion'));
        $exclusion[] = $this->getUser()->getId();
        $users = UserQuery::getFromAutocomplete($request->getParameter('query'), $exclusion);

        foreach($users as $user)
        {
            if ($user['Img'] == '' || $user['Img'] == null) {
              $user['Img'] = '/uploads/utenti/avatar/50/default.jpg';
            } else {
              $user['Img'] = '/uploads/utenti/avatar/50/' . $user['Img'];
            }
            $resp[$user['FirstName'].' '.$user['LastName']] = $user;
        }   

        $this->getResponse()->setContentType('application/json');
        return $this->renderText(json_encode($resp));
    }
}
