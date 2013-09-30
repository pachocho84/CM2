<?php

namespace CM\CMBundle\Controller;

use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Form\EventType;

class EventController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        
        $events = $em->getRepository('CMBundle:Event')->getEvents('it');//$this->get('session')->get('locale'));
    
        return $this->render('CMBundle:Event:index.html.twig', array(
        	'events' => $events
        ));
    }
    
    public function showAction($id)
    {
        $event = $this->getEvent($id);
        
        return $this->render('CMBundle:Event:show.html.twig', array(
            'event'      => $event
        ));
    }
    
    public function newAction()
    {
        $event = new Event;
        
        $form = $this->createForm(new EventType(), $event);

        return $this->render('CMBundle:Event:form.html.twig', array(
            'event' => $event,
            'form'   => $form->createView()
        ));
    }

    public function createAction(Request $request)
    {
        $event = new Event;
        
        $form = $this->createForm(new EventType(), $event);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($event);
            $em->flush();
			
            return $this->redirect($this->generateUrl('cm_event_show', array('id' => $event->getId())));
        }

        return $this->render('CMBundle:Event:create.html.twig', array(
            'event' => $event,
            'form'    => $form->createView()
        ));
    }
    
    public function changeLocaleAction($locale)
    {
        $this->get('session')->set('locale', $locale);
    }
    
    public function getEvent($id)
    {
        $em = $this->getDoctrine()->getManager();
        
	    return $em->getRepository('CMBundle:Event')->getEvent($id, $this->get('session')->get('locale'));
    }
}
