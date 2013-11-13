<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Form\CommentType;

/**
 * @Route("/wall")
 */
class WallController extends Controller
{
    /**
     * @Route("/{page}", name="wall_index", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('exclude' => array($this->getUser()->getId())));
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination));
        }

        return array('posts' => $pagination);
    }

    /**
     * @Route("/{postId}/update", name="wall_update", requirements={"postId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function postsAction(Request $request, $postId)
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('after' => $postId, 'exclude' => array($this->getUser()->getId()), 'paginate' => false));

        return array('posts' => $posts);
    }

    /**
     * @Route("/show/{postId}", name="wall_show", requirements={"postId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function showAction(Request $request, $postId)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('CMBundle:Post')->findOneById($postId);

        return array('post' => $post);
    }
}