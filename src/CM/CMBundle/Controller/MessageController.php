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
use FOS\MessageBundle\FormType\NewThreadMultipleMessageFormType;

/**
 * @Route("/messages")
 */
class MessageController extends Controller
{
    /**
     * @Route("/inbox/{page}", name = "message_index", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('CMBundle:MessageThread')->getActiveThreads(array('userId' => $this->getUser()->getId()));
        
        $pagination = $this->get('knp_paginator')->paginate($messages, $page, 12);

        if ($request->isXmlHttpRequest() && !$request->get('outgoing')) {
            return $this->render('CMBundle:Message:messageList.html.twig', array('messages' => $pagination));
        }

        return array(
            'messages' => $pagination
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

        $form = $this->get('fos_message.new_thread_form.factory')->create()->add('save', 'submit');
        $formHandler = $this->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('message_show', array(
                'threadId' => $message->getThread()->getId()
            )));
        }

        return array(
            'form' => $form->createView(),
            'user' => $user
        );
    }

    /**
     * @Route("/{threadId}/{page}", name="message_show", requirements={"threadId" = "\d+", "page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function showAction($threadId, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('CMBundle:MessageThread')->getThread($threadId, array('userId' => $this->getUser()->getId()));
        $pagination = $this->get('knp_paginator')->paginate($messages, $page, 15);

        // var_dump($messages, $pagination[0]->getThread());die;

        $thread = $pagination[0]->getThread();

        if (is_null($thread)) {
            throw new NotFoundHttpException($this->get('translator')->trans('Thread not found.', array(), 'http-errors'));
        }

        $form = $this->container->get('fos_message.reply_form.factory')->create($thread);
        $formHandler = $this->container->get('fos_message.reply_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('message_show', array(
                'threadId' => $threadId
            )));
        }

        return array(
            'thread' => $thread,
            'messages' => $pagination,
            'form' => $form->createView(),
        );
    }

    protected function getProvider()
    {
        return $this->container->get('fos_message.provider');
    }
}