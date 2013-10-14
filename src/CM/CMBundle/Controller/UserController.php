<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/{username}", defaults={"username": null})
 */
class UserController extends Controller
{
    /**
     * @Route("/", name="user_show")
     * @Template
     */
    public function showAction($username)
    {
        return array('username' => $username);
    }
    
    /**
     * @Route("/events", name="user_events")
     * @Template
     */
    public function eventsAction(Request $request, $username)
    {
		$em = $this->getDoctrine()->getManager();
		
		$user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $username));
    
        return array(
            'request' => $request,
            'page' => 1,
            'category_slug' => null,
            'user' => $user
        );
    }
}
