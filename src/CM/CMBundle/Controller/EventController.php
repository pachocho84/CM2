<?php

namespace CM\CMBundle\Controller;

use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\EventDateType;

class EventController extends Controller
{
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
               
        $events = $em->getRepository('CMBundle:Event')->getEvents($this->container->getParameter('locale'));//$this->get('session')->get('locale'));
    
        return $this->render('CMBundle:Event:index.html.twig', array(
	       	'events' => $events
        ));
    }
    
    public function showAction($id)
    {
        $event = $this->getEvent($id, $this->container->getParameter('locale'));
        
        return $this->render('CMBundle:Event:show.html.twig', array(
            'event' => $event
        ));
    }
    
    public function newAction()
    {
        $event = new Event;
        $eventDate = new EventDate;
        $eventDate->setLocation('casa mia');
        $event->addEventDate($eventDate);
        $eventDate1 = new EventDate;
        $eventDate1->setLocation('casa mia');
        $event->addEventDate($eventDate1);
        
        $form = $this->createForm(new EventType, $event);

        return $this->render('CMBundle:Event:form.html.twig', array(
            'form' => $form->createView()
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
            'form' => $form->createView()
        ));
    }
}
