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
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Form\EventType;

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
     * @Route("/protagonist/add", name="user_add_protagonist")
     * @Template
     */
    public function addProtagonistAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user_id = $request->query->get('user_id');
        $user = $em->getRepository('CMBundle:User')->findOneById($user_id);

        $protagonist_new_id = $request->query->get('protagonist_new_id');

        $event = new Event;

        foreach (range(0, $protagonist_new_id - 1) as $i) {
            $event->addUser($user);
        }

        $event->addUser(
            $user,
            true, // admin
            EntityUser::STATUS_ACTIVE,
            true // notifications
        );

        // $entityUsers = new ArrayCollection;
        // $entityUser = new EntityUser;

        // $userTags = $user->getUserTags();
        // $userTagsIds = array();
        // foreach ($userTags as $userTag) {
        //     $userTagsIds[] = $userTag->getId();
        // }
        // $entityUser->setUser($user)
        //     ->setStatus(EntityUser::STATUS_PENDING)
        //     ->addUserTags($userTagsIds);
        // $entityUsers[] = $entityUser;

        $form = $this->createForm(new EventType, $event, array(
            'omit_collection_item' => range(0, $protagonist_new_id - 1),
            'cascade_validation' => true,
            'user_tags' => $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ));
        
        return array(
            'skip' => true,
            'user_id' => $user->getId(),
            'entityUsers' => $form->createView()['entityUsers'],
            'user_id' => $user_id,
            'protagonist_new_id' => $protagonist_new_id
        );
    }

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
                $user['img'] = '/uploads/utenti/avatar/50/default.jpg';
            } else {
                $user['img'] = '/uploads/utenti/avatar/50/'.$user['img'];
            }
            $user['fullname'] = $user['firstName'].' '.$user['lastName'];
            $results[] = $user;
        }

        return new JsonResponse($results);
    }
}
