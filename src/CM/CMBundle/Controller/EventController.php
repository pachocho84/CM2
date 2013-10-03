<?php

namespace CM\CMBundle\Controller;

use \DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Locale;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\MultipleImagesType;
use CM\CMBundle\Utility\UploadHandler;

/**
 * @Route("/{_locale}/event", defaults={"_locale" = "en"}, requirements={"_locale" = "^[a-z]{2}$"})
 */
class EventController extends Controller
{
    /**
     * @Route("/", name = "event_index")
     * @Template
     */
    public function indexAction(Request $request, $_locale)
    {
        $em = $this->getDoctrine()->getManager();
        $events = $em->getRepository('CMBundle:Event')->getEvents(array('locale' => $_locale));

        return array('locale' => $_locale, 'events' => $events, 'test' => 'test');
    }
    
    /**
     * @Route("/{id}/{slug}", name="event_show", requirements={"id" = "\d+", "_locale" = "en|fr|it"})
     * @Template
     */
    public function showAction(Request $request, $_locale, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('CMBundle:Event')->getEvent($id, $_locale);

        $images = new ArrayCollection();
        $image = new Image;

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

            return $this->redirect($this->generateUrl('event_show', array(
            	'id' => $event->getId(),
            	'slug' => $event->getSlug()
            )));
        }
        
        return array('event' => $event, 'form' => $form->createView());
    }
    
    /**
     * @Route("/new", name="event_new") 
     * @Route("/{id}/{slug}/edit", name="event_edit", requirements={"id" = "\d+", "_locale" = "en|fr|it"}) 
     * @Template
     */
    public function editAction(Request $request, $_locale, $id = null, $slug = null)
    {
    	$event;
    	if ($id == null || $slug == null) {
        	$event = new Event;
			$event->addEventDate(new EventDate);
			$event->addImage(new Image);
		}
		else {
			$em = $this->getDoctrine()->getManager();
	        $event = $em->getRepository('CMBundle:Event')->getEvent($id, $_locale);
	        // TODO: retrieve images from event
		}
        
        // TODO: retrieve locales from user

        $form = $this->createForm(new EventType(), $event, array(
        	'action' => $this->generateUrl('event_new'),
        	'cascade_validation' => true,
        	'locales' => array('en'/* , 'fr', 'it' */),
        	'locale' => $_locale
        ))->add('save', 'submit');
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
        	$em = $this->getDoctrine()->getEntityManager();
            $em->persist($event);
            $em->flush();
			
            return $this->redirect($this->generateUrl('event_show', array(
            	'id' => $event->getId(),
            	'slug' => $event->getSlug()
            )));
        }

        return array('form' => $form->createView());
    }
}
