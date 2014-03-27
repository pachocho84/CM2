<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Process\Exception\RuntimeException;
use JMS\SecurityExtraBundle\Annotation as JMS;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\MessageThread;
use CM\CMBundle\Entity\MessageMetadata;
use FOS\MessageBundle\FormType\NewThreadMultipleMessageFormType;

/**
 * @Route("/messages")
 */
class MessageController extends Controller
{
    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }

    /**
     * @Route("/inbox/{page}", name = "message_index", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function indexAction(Request $request, $page = 1, $threadId = null)
    {
        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('CMBundle:MessageThread')->getActiveThreads(array('userId' => $this->getUser()->getId()));
        $pagination = $this->get('knp_paginator')->paginate($messages, $page, 12);

        $em->getRepository('CMBundle:MessageThread')->setUnread($this->getUser()->getId());

        if ($request->isXmlHttpRequest() && !$request->get('outgoing')) {
            return $this->render('CMBundle:Message:messageList.html.twig', array('messages' => $pagination));
        }

        return array(
            'messages' => $pagination,
            'threadId' => $threadId
        );
    }

    /**
     * @Route("/new/{userId}", name="message_new", requirements={"userId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function newAction(Request $request, $userId = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($userId == $this->getUser()->getId()) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if (!is_null($userId)) {
            $user = $em->getRepository('CMBundle:User')->findOneById($userId);
        
            if (!$user) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
        }

        $form = $this->get('fos_message.new_thread_form.factory')->create()->add('send', 'submit', array('attr' => array('class' => 'btn btn-primary')));
        $formHandler = $this->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('message_show', array('threadId' => $message->getThread()->getId())));
        }

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Message:newForm.html.twig', array(
                'form' => $form->createView(),
                'formAction' => $this->generateUrl('message_new', array('userId' => $userId)),
                'user' => $user
            ));
        }

        return array(
            'form' => $form->createView(),
            'user' => $user
        );
    }

    /**
     * @Route("/{threadId}/respond", name="message_respond", requirements={"threadId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function respondAction($threadId)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository('CMBundle:MessageThread')->setRead($threadId, $this->getUser()->getId());

        $thread = $em->getRepository('CMBundle:MessageThread')->getThread($threadId, $this->getUser()->getId());

        if (is_null($thread)) {
            throw new NotFoundHttpException($this->get('translator')->trans('Thread not found.', array(), 'http-errors'));
        }

        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');

        if ($message = $formHandler->process($form)) {
            return $this->render('CMBundle:Message:object.html.twig', array('message' => $message));
        }

        throw new NotFoundHttpException($this->get('translator')->trans('Error.', array(), 'http-errors'));
    }

    /**
     * @Route("/{threadId}/{messageId}/remove", name="message_delete_message", requirements={"threadId" = "\d+", "messageId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function deleteMessageAction($threadId, $messageId)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository('CMBundle:MessageThread')->deleteFromMessage($messageId, $this->getUser()->getId());

        return $this->forward('CMBundle:Message:show', array('threadId' => $threadId));
    }

    /**
     * @Route("/{threadId}/delete", name="message_delete_thread", requirements={"threadId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function deleteThreadAction($threadId)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository('CMBundle:MessageThread')->deleteFromThread($threadId, $this->getUser()->getId());

        if ($this->isXmlHttpRequest()) {
            return new Response;
        }

        return $this->forward('CMBundle:Message:index');
    }



    /**
     * @Route("/{threadId}/{page}", name="message_show", requirements={"threadId" = "\d+", "page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function showAction(Request $request, $threadId, $page = 1, $force = false)
    {
        if (!$force && !$request->isXmlHttpRequest()) {
            return $this->forward('CMBundle:Message:index', array('threadId' => $threadId));
        }

        $em = $this->getDoctrine()->getManager();

        $em->getRepository('CMBundle:MessageThread')->setRead($threadId, $this->getUser()->getId());

        $messages = $em->getRepository('CMBundle:MessageThread')->getMessages($threadId, array('userId' => $this->getUser()->getId()));
        $pagination = $this->get('knp_paginator')->paginate($messages, $page, 15);

        // var_dump($messages, $pagination[0]->getThread());die;

        $thread = $pagination[0]->getThread();

        if (is_null($thread)) {
            throw new NotFoundHttpException($this->get('translator')->trans('Thread not found.', array(), 'http-errors'));
        }

        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');

        return array(
            'thread' => $thread,
            'messages' => $pagination,
            'form' => $form->createView(),
        );
    }
}