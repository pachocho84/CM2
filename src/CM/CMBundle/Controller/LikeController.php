<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
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
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
	          throw new HttpException('Unauthorized access.', 401); 
        }
    
        $em = $this->getDoctrine()->getManager();
  
        if (!$em->getRepository('CMBundle:Like')->checkIfUserLikesIt($this->getUser(), $type, $id)) {        
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
                'likes' => $this->renderView('CMBundle:Like:like.html.twig', array('post' => $post)), 
                'likeActions' => $this->renderView('CMBundle:Like:likeActions.html.twig', array('post' => $post)),
                'likeCount' => $this->renderView('CMBundle:Like:likeCount.html.twig', array('post' => $post))
            ));
        }
        
        $this->get('session')->getFlashBag('confirm', 'You like this.');
        return new RedirectResponse($request->get('referer'));
    }
    
    /**
     * @Route("/unlike/{type}/{id}", name="unlike", requirements={"type" = "post|image"})
     */
    public function unlikeAction(Request $request, $type, $id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
	          throw new HttpException('Unauthorized access.', 401); 
        }
        
        $em = $this->getDoctrine()->getManager();
        $like = $em->getRepository('CMBundle:Like')->findOneBy(array(
            $type => $id,
            'user' => $this->getUser()
        ));
        $em->remove($like);
        $em->flush();

        if ($request->isXmlHttpRequest()) { 
            if ($type == 'post') {
                $post = $em->getRepository('CMBundle:Post')->find($id);
            } elseif ($type == 'image') {
                $post = $em->getRepository('CMBundle:Image')->find($id);
            }
            
            if (is_null($post)) {
                throw $this->createNotFoundException('Bad request.');
            }

            return new JsonResponse(array(
                'likes' => $this->renderView('CMBundle:Like:like.html.twig', array('post' => $post)), 
                'likeActions' => $this->renderView('CMBundle:Like:likeActions.html.twig', array('post' => $post)),
                'likeCount' => $this->renderView('CMBundle:Like:likeCount.html.twig', array('post' => $post)),
            ));
        }

        $this->get('session')->getFlashBag('confirm', 'You don\'t like this anymore.');
        return new RedirectResponse($request->get('referer'));
    }

    /**
     * @Route("/who_likes/{type}/{id}", name="who_likes_it", requirements={"_request_type"="json"})
     */
    public function whoLikesItAction(Request $request, $type, $id)
    {
        return new Response('OK');

        // $em = $this->getDoctrine()->getManager();
        // $whoLikesIt = $em->getRepository('CMBundle:Like')->whoLikesIt($type, $id);
        
        // return $this->render('utenti/dialog', array(
        //     'title'             => 'Who likes it',
        //     'users'             => $whoLikesIt, 
        //     'whoImFanOf'    => false // $this->getUser()->isAuthenticated() ? FansQuery::whoImFanOf() : false
        // ));
    }
}