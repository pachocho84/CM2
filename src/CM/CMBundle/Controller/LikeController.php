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
  
        if ($em->getRepository('CMBundle:Like')->checkIfUserLikesIt($this->getUser(), $type, $id) == false) {        
            $like = new Like();
            $like->setUser($this->getUser());
            if ($type == 'post') {
                $post = $em->getRepository('CMBundle:Post')->find($id);
                $like->setPost($post);
            } elseif ($type == 'image') {
                $post = $em->getRepository('CMBundle:Image')->find($id);
                $like->setImage($post);
            }
            $em->persist($like);
            $em->flush();
        } else {
            throw new RuntimeException('Already liked');
        }
        
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(array(
                'likes' => $this->render('CMBundle:Event:Like:like.html.twig', array('post' => $post)), 
                'likeActions' => $this->render('CMBundle:Event:Like:likeActions.html.twig', array('post' => $post)),
                'likeCount' => $this->render('CMBundle:Event:Like:likeCount.html.twig', array('post' => $post))
            ));
        }
        
        $this->get('session')->getFlashBag('confirm', 'You like this.');
        return new RedirectResponse($request->get('referer'));
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
                'likes'             => $this->getPartial('like', array('post' => $post)), 
                'likeActions' => $this->getPartial('likeActions', array('post' => $post)),
                'likeCount'     => $this->getPartial('likeCount', array('post' => $post)),
            )));
        }
    
        $this->getUser()->setFlash('conferma', $this->getContext()->getI18N()->__('You don\'t like this anymore.'));
        return new RedirectResponse($request->headers->get('referer'));
    }

    /**
     * @Route("/who_likes/{type}/{id}", name="who_likes_it")
     */
    public function whoLikesItAction(Request $request, $type, $id)
    {   
        if (!$request->isXmlHttpRequest()) {
            throw $this->createNotFoundException('This page can only be requested by AJAX.');
        }

        $em = $this->getDoctrine()->getManager();
        $whoLikesIt = $em->getRepository('CMBundle:Like')->whoLikesIt($type, $id);
        
        return $this->render('utenti/dialog', array(
            'title'             => 'Who likes it',
            'users'             => $whoLikesIt, 
            'whoImFanOf'    => false // $this->getUser()->isAuthenticated() ? FansQuery::whoImFanOf() : false
        ));
    }
}