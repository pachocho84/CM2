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
     * @Route("/{post_id}", name="comment_index", requirements={"post_id" = "\d+"})
     * @Template
     */
    public function commentsAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        // $comments = $em->getRepository('CMBundle:Comment')->getCommentsFor('post', $post->getId());

        // echo '<pre>'.var_dump($comments); die;

        $form = null;
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $comment = new Comment;

            $form = $this->createForm(new CommentType(), $comment, array(
                'action' => $this->generateUrl('comment_new', array(
                    'post_id' => $post->getId()
                )),
                'cascade_validation' => true
            ))->add('save', 'submit')->createView();
        }

        return array(
            'post' => $post,
            'comments' => $post->getComments(),
            'form' => $form
        );
        // $this->comments = $this->post->getComments();
        
        // if ($this->getContext()->getUser()->isAuthenticated())
        // {
        //     if (get_class($this->post) == 'Post') {
        //         $this->form = new CommentForm(null, array('post_id' => $this->post->getId()));
        //     } elseif (get_class($this->post) == 'Image') {
        //         $this->form = new CommentForm(null, array('image_id' => $this->post->getId()));
        //     }
        // }
    }
    
    /**
     * @Route("/new/{post_id}", name="comment_new", requirements={"post_id" = "\d+"}) 
     * @Template
     */
      public function newAction(Request $request, $post_id)
    {
        $comment = new Comment;
        $form = $this->createForm(new CommentType(), $comment)->add('save', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $comment
                ->setUser($this->getUser())
                ->setPost($em->getRepository('CMBundle:Post')->findOneById($post_id));
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
            $this->get('session')->getFlashBag('error', 'Please fill in all the required fields.');
        }

        return new RedirectResponse($request->get('referer'));
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
            
        //     if ($request->isXmlHttpRequest()) {         
        //         if ($comment->getPostId()) {
        //             $post = $comment->getPost();
        //         } elseif ($comment->getImageId()) {
        //             $post = $comment->getImage();
        //         }
        //         return $this->renderPartial('commentCount', array('post' => $post));
        //     }
            
        //     $this->getUser()->setFlash('conferma', $this->getContext()->getI18N()->__('Comment successfully deleted.'));
        //     $this->redirect($request->getReferer());
    }
}