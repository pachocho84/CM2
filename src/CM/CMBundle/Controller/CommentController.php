<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Comment;

/**
 * @Route("/comments")
 */
class CommentController extends Controller
{
    /**
     * @Route("/{post_id}", name="comment_index")
     * @Template
     */
    public function commentsAction(Post $post)
    {
        $em = $this->getDoctrine()->getManager();
        // $comments = $em->getRepository('CMBundle:Comment')->getCommentsFor('post', $post->getId());

        // echo '<pre>'.var_dump($comments); die;

        $form = null;
        if ($this->get('security.context')->isGranted('IS_ACCESS_REMEMBERED')) {
            $form = $this->createForm(new CommentType(), array(
                'action' => $this->generateUrl('comment_new', array(
                    'id' => $event->getId(),
                    'slug' => $event->getSlug()
                )),
                'cascade_validation' => true
            ))->add('save', 'submit');
        }

        return array(
            'postId' => $post->getId(),
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
     * @Route("/new", name="comment_new") 
     * @Route("/{id}/{slug}/edit", name="event_edit", requirements={"id" = "\d+"}) 
     * @Template
     */
      public function newAction(Request $request)
    {     
        $this->form = new CommentForm();
        
        if ($this->form->bindAndSave($request->getParameter('comment'))) {
        
            if ($request->isXmlHttpRequest()) {         
                if ($this->form->getObject()->getPostId()) {
                    $commentCount = $this->getPartial('commentCount', array('post' => PostQuery::create()->findOneById($this->form->getObject()->getPostId())));
                } elseif ($this->form->getObject()->getImageId()) {
                    $commentCount = $this->getPartial('commentCount', array('post' => $this->form->getObject()->getImage()));
                }
                $this->getResponse()->setContentType('application/json');   
                return $this->renderText(json_encode(array(
                    'comment' => $this->getPartial('comment/comment', array('comment' => $this->form->getObject())),
                    'commentCount' => $commentCount
                )));
            }
            
            $this->getUser()->setFlash('conferma', $this->getContext()->getI18N()->__('Comment successfully added.'));
        } else {
            echo $this->form;
            $this->getUser()->setFlash('errore', $this->getContext()->getI18N()->__('Please fill in all the required fields.'));
        }
        
        $this->redirect($request->getReferer());
    }

    /**
     * @Route("/delete/{id}", name="comment_delete")
     */ 
    public function executeDelete(sfWebRequest $request)
    {
        $comment = CommentQuery::create()->findOneById($request->getParameter('comment_id'));

        $this->forward404Unless($this->getUser()->canManage($comment) && $comment, $this->getContext()->getI18N()->__('Bad request.'));
            
            $comment->delete();
            
            if ($request->isXmlHttpRequest()) {         
                if ($comment->getPostId()) {
                    $post = $comment->getPost();
                } elseif ($comment->getImageId()) {
                    $post = $comment->getImage();
                }
                return $this->renderPartial('commentCount', array('post' => $post));
            }
            
            $this->getUser()->setFlash('conferma', $this->getContext()->getI18N()->__('Comment successfully deleted.'));
            $this->redirect($request->getReferer());
    }
}