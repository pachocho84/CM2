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
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Form\CommentType;

/**
 * @Route("/comments")
 */
class CommentController extends Controller
{
    /**
     * @Route("/new/{postId}", name="comment_new", requirements={"postId" = "\d+"}) 
     * @Template
     */
    public function commentsAction(Request $request, Post $post = null, $postId = null)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($post)) {
            $post = $em->getRepository('CMBundle:Post')->findOneById($postId);
        }

        $form = null;
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $comment = new Comment;
            $form = $this->createForm(new CommentType, $comment, array(
                'action' => $this->generateUrl('comment_new', array(
                    'postId' => $post->getId()
                )),
                'cascade_validation' => true
            ));

            $form->handleRequest($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $comment->setUser($this->getUser())
                    ->setPost($post);
                $em->persist($comment);
                $em->flush();
    
                if ($request->isXmlHttpRequest()) {
                    if ($comment->getPost()->getId()) {
                        $commentCount = $this->renderView('CMBundle:Comment:commentCount.html.twig', array('post' => $comment->getPost()));
                    } elseif ($comment->getImage()->getId()) {
                        $commentCount = $this->renderView('CMBundle:Comment:commentCount.html.twig', array('post' => $comment->getImage()));
                    }
    
                    return new JsonResponse(array(
                        'comment' => $this->renderView('CMBundle:Comment:comment.html.twig', array('comment' => $comment)),
                        'commentCount' => $commentCount
                    ));
                }
    
                $this->get('session')->getFlashBag('confirm', 'Comment successfully added.');
            } else {
                $form = $form->createView();
            }
        }

        return array(
            'post' => $post,
            'comments' => $post->getComments(),
            'form' => $form
        );
    }

    /**
     * @Route("/delete/{id}", name="comment_delete", requirements={"id" = "\d+"})
     */ 
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $comment = $em->getRepository('CMBundle:Comment')->findOneById($id);

        if (!$this->get('cm.user_authentication')->canManage($comment)) {
            throw new HttpException(401, 'Unauthorized access.');
        }
            
        $em->remove($comment);
        $em->flush();

        if ($request->isXmlHttpRequest()) {
            if ($comment->getPost()->getId()) {
                $commentCount = $this->renderView('CMBundle:Comment:commentCount.html.twig', array('post' => $comment->getPost()));
            } elseif ($comment->getImage()->getId()) {
                $commentCount = $this->renderView('CMBundle:Comment:commentCount.html.twig', array('post' => $comment->getImage()));
            }

            return new JsonResponse(array(
                'commentCount' => $commentCount
            ));
        }

        $this->get('session')->getFlashBag('confirm', 'You don\'t like this anymore.');
        return new RedirectResponse($request->get('referer'));
    }
}