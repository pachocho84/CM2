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
     * @Route("/new/{id}/{isImage}", name="comment_new", requirements={"id" = "\d+"})
     * @Route("/edit/{commentId}/{id}/{isImage}", name="comment_edit", requirements={"id" = "\d+", "commentId" = "\d+"})
     * @Route("/entity/new/{id}", name="comment_entity_new", requirements={"id" = "\d+"})
     * @Template
     */
    public function commentsAction(Request $request, $post = null, $id = null, $commentId = null, $isImage = false)
    {
        $em = $this->getDoctrine()->getManager();

        if (is_null($post)) {
            if (!$isImage) {
                $post = $em->getRepository('CMBundle:Post')->getPostWithComments($id);
                $image = null;
            } else {
                $post = null;
                $image = $em->getRepository('CMBundle:Image')->getImageWithComments($id);
            }
        } else {
            $id = $post->getId();
        }

        $form = null;
        if ($this->get('security.context')->isGranted('ROLE_USER')) {

            if ($request->get('_route') == 'comment_edit') {
                $comment = $em->getRepository('CMBundle:Comment')->findOneById($commentId);

                $text = trim($request->get('cm_cmbundle_comment')['comment']);
                if ($text == '' || Text == $comment->getComment()) {
                    throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                } else {
                    $comment->setComment($text);
                    $isValid = true;
                }
            } else {
                $comment = new Comment;
                $form = $this->createForm(new CommentType, $comment, array(
                    'action' => $this->generateUrl('comment_new', array(
                        'id' => $id,
                        'isImage' => $isImage
                    )),
                    'cascade_validation' => true
                ));

                $form->handleRequest($request);

                $isValid = $form->isValid();

                $comment->setUser($this->getUser())
                    ->setPost($post)
                    ->setImage($image);
            }

            if ($isValid) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($comment);
                $em->flush();

                if ($request->isXmlHttpRequest()) {
                    if ($request->get('_route') == 'comment_entity_new') {
                        $post = $em->getRepository('CMBundle:Post')->findOneBy(array('object' => $comment->className(), 'objectIds' => ','.$comment->getId().','));
                        
                        return new JsonResponse(array(
                            'comment' => $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post, 'comment' => $comment, 'inEntity' => true, 'singleComment' => true))
                        ));
                    } elseif (!is_null($post)) {
                        $commentCount = $this->renderView('CMBundle:Comment:commentCount.html.twig', array('post' => $comment->getPost()));
                    } else {
                        $commentCount = $this->renderView('CMBundle:Comment:commentCount.html.twig', array('post' => $comment->getImage()));
                    }
        
                    return new JsonResponse(array(
                        'comment' => $this->renderView('CMBundle:Comment:comment.html.twig', array('comment' => $comment)),
                        'commentCount' => $commentCount
                    ));
                } else {
                    return new RedirectResponse($request->getUri());
                }
    
                $this->get('session')->getFlashBag('confirm', 'Comment successfully added.');
            } else {
                if ($request->get('_route') == 'comment_entity_new') {
                    throw new HttpException(400, $this->get('translator')->trans('Error.', array(), 'http-errors'));
                }
                $form = $form->createView();
            }
        }

        return array(
            'link' => $link,
            'comments' => is_null($post) ? $image->getComments() : $post->getComments(),
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

        if (!$this->get('cm.user_authentication')->canManage($comment) && !$this->get('cm.user_authentication')->canManage($comment->getPost()->getEntity())) {
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