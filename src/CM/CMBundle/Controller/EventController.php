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
    public function showAction($_locale, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        $event = $em->getRepository('CMBundle:Event')->getEvent($id, $_locale);
        
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
        
        if (count($this->get('validator')->validate($event)) > 0) {
        	return new Response(print_r($errors, true));
		}
        
        if ($form->isValid()) {
        	// sanitize uploaded file name and move it in the uploads dir
        	$imageFile = $form['images'][0]['img']->getData();
        	$fileDir = $this->container->getParameter('kernel.root_dir').'/../web'.$this->container->getParameter('uploads.images_full');
        	$fileName = md5($imageFile->getClientOriginalName().time().uniqid()).'.'.$imageFile->guessExtension(); // FIXME: doesn't work with bmp files

        	$imageFile->move($fileDir, $fileName);

        	$event->getImages()[0]->setImg($fileName);

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
