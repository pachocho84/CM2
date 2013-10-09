<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Like;

class LikeController extends Controller
{
	/**
	 * @Route("/like/{type}/{id}", name="like", requirements={"type" = "post|image"})
	 */
    public function likeAction(Request $request, $type, $id)
    {
  	    $em = $this->getDoctrine()->getManager();
  
		if ($em->getRepository('CMBundle:Like')->checkIfILikeIt($type, $id) == false) {		
			$like = new Like();
			$like->setUser($this->getUser());
			if ($type == 'post') {
				$like->setPost($em->getRepository('CMBundle:Post')->find($id));
				$post = $em->getRepository('CMBundle:Post')->find($id);
			} elseif ($type == 'image') {
				$like->setImage($em->getRepository('CMBundle:Image')->find($id));
				$post = $em->getRepository('CMBundle:Post')->find($id);
			}
			$em->persist($like);
			$em->flush();
		} else {
    		throw new RuntimeException('Already liked');
		}
		
		if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
		        'likes' => $this->render('CMBundle:Event:Like:like.html.twig', array(
		            'post' => $post
		        )), 
		        'likeActions' => $this->render('CMBundle:Event:Like:likeActions.html.twig', array('post' => $post)),
		        'likeCount' => $this->render('CMBundle:Event:Like:likeCount.html.twig', array('post' => $post))
		    ));
		}
		
		$this->get('session')->getFlashBag('confirm', 'You like this.');
		return new RedirectResponse($request->get('referer'));
    }
  
    public function jsonLikeAction($post)
    {

    }
	
	/**
	 * @Route("/unlike/{type}/{id}", name="unlike", requirements={"type" = "post|image"})
	 */
  public function unlikeAction(Request $request)
	{		
		$like = LikeQuery::create()->filterByArray(array(ucfirst($request->getParameter('type')).'Id' => $request->getParameter('id'), 'UserId' => $this->getUser()->getId()))->findOne();
		$like->delete();
		
		if ($request->isXmlHttpRequest()) {					
			if ($request->getParameter('type') == 'post') {
				$post = PostQuery::create()->findOneById($request->getParameter('id'));
			} elseif ($request->getParameter('type') == 'image') {
				$post = ImageQuery::create()->findOneById($request->getParameter('id'));
			}
    	$this->forward404Unless($post, $this->getContext()->getI18N()->__('Bad request.'));

			return $this->renderText(json_encode(array(
				'likes' 			=> $this->getPartial('like', array('post' => $post)), 
				'likeActions' => $this->getPartial('likeActions', array('post' => $post)),
				'likeCount' 	=> $this->getPartial('likeCount', array('post' => $post)),
			)));
		}
	
		$this->getUser()->setFlash('conferma', $this->getContext()->getI18N()->__('You don\'t like this anymore.'));
		return new RedirectResponse($request->headers->get('referer'));
  }
}