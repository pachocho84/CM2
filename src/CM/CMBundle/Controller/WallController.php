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
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $posts = $em->getRepository('CMBundle:Post')->getLastPosts();
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 15);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Wall:posts.html.twig', array('posts' => $pagination));
        }

        return array('posts' => $pagination);
    }
    
    /**
     * @Route("/entity/{id}/{page}", name="wall_entity", requirements={"id" = "\d+", "page" = "\d+"})
     * @Route("/entity/{id}/{lastUpdated}/update", name="wall_entity_update")
     * @Route("/entity/update", name="_update")
     * @Route("/entity/update", name="")
     * @Template
     */
    public function entityAction(Request $request, $id, $page = 1, $lastUpdated = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (is_null($lastUpdated)) {
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('entityId' => $id, 'locale' => $request->getLocale()));
        } else {
            $after = new \DateTime;
            $after->setTimestamp($lastUpdated);
            $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('entityId' => $id, 'locale' => $request->getLocale(), 'after' => $after));
        }
        $pagination = $this->get('knp_paginator')->paginate($posts, $page, 10);

        $comment = new Comment;
        $form = $this->createForm(new CommentType, $comment, array(
            'action' => $this->generateUrl('comment_entity_new', array(
                'id' => $pagination[0]->getEntity()->getPost()->getId()
            )),
            'cascade_validation' => true
        ));

        $form = $form->createView();
                
        return array(
            'posts' => $pagination,
            'inEntity' => true,
            'commentForm' => $form
        );
    }

    /**
     * @Route("/{lastUpdated}/update", name="wall_index_update")
     * @Template
     */
    public function postsAction(Request $request, $lastUpdated)
    {
        $em = $this->getDoctrine()->getManager();

        $after = new \DateTime;
        $after->setTimestamp($lastUpdated);
        $posts = $em->getRepository('CMBundle:Post')->getLastPosts(array('after' => $after, 'paginate' => false));

        return array('posts' => $posts);
    }

    /**
     * @Route("/show/{postId}", name="wall_show", requirements={"postId" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $postId)
    {
        $em = $this->getDoctrine()->getManager();

        $post = $em->getRepository('CMBundle:Post')->getPostWithSocial($postId);

        return array('post' => $post);
    }
}