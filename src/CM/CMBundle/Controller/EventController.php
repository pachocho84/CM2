<?php

namespace CM\CMBundle\Controller;

use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\EventDateType;

/**
 * @Route("/event")
 */
class EventController extends Controller
{
    /**
     * @Route("/{_locale}", name = "event_index", defaults={"_locale" = "en"}, requirements={"_locale" = "en|fr|it"})
     * @Template("CMBundle:Event:index.html.twig")
     */
    public function indexAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('CMBundle:Event')->getEvents(array('locale' => $_locale));
    
        return array('events' => $events);
    }
    
    /**
     * @Route("/{_locale}/{id}/{slug}", defaults={"_locale" = "en"}, requirements={"id" = "\d+", "_locale" = "en|fr|it"}, name="event_show")
     * @Template("CMBundle:Event:show.html.twig")
     */
    public function showAction($id, $slug, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('CMBundle:Event')->getEvent($id, $_locale);
        
        return array('event' => $event);
    }
    
    /**
     * @Route("/new", name="event_new") 
     * @Template("CMBundle:Event:form.html.twig")
     */
    public function newAction()
    {
        $event = new Event;
        $event->addEventDate(new EventDate);
        
        $form = $this->createForm(new EventType, $event);

        return array('form' => $form->createView());
    }

    /**
     * @Route("/create", name="event_create") 
     * @Method("POST")
     * @Template("CMBundle:Event:form.html.twig")
     */
    public function createAction(Request $request)
    {
        $event = new Event;
        
        $form = $this->createForm(new EventType(), $event);
/*         $form->bind($request); */
		$form->handleRequest($request);
        

        if ($form->isValid()) {
        	echo 'valid'; die;
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($event);
            $em->flush();
			
            return $this->redirect($this->generateUrl('event_show', array('id' => $event->getId())));
        }

        return array('event' => $event, 'form' => $form->createView());
    }
    
    /**
     * @Route("/locale", name="event_locale", 
     */
}
