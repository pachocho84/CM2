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
use CM\CMBundle\Entity\Post;
use FOS\MessageBundle\FormType\NewThreadMultipleMessageFormType;

/**
 * @Route("/messages")
 */
class MessageController extends Controller
{
    /**
     * @Route("/{page}", name = "message_index", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $threads = $em->getRepository('CMBundle:MessageThread')->getActiveThreads(array('userId' => $this->getUser()->getId()));
        
        $pagination = $this->get('knp_paginator')->paginate($threads, $page, 12);

        if ($request->isXmlHttpRequest() && !$request->get('outgoing')) {
            return $this->render('CMBundle:Message:messageList.html.twig', array('threads' => $pagination));
        }

        return array(
            'threads' => $pagination
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

        if (!is_null($userId)) {
            $user = $em->getRepository('CMBundle:User')->findOneById($userId);
        
            if (!$user) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
        }

        $form = $this->get('fos_message.new_thread_form.factory')->create()->add('save', 'submit');
        $formHandler = $this->get('fos_message.new_thread_form.handler');

        if ($message = $formHandler->process($form)) {
            return new RedirectResponse($this->container->get('router')->generate('fos_message_thread_view', array(
                'threadId' => $message->getThread()->getId()
            )));
        }
        
        // $composer = $container->get('fos_message.composer');

        // $message = $composer->newThread()
        //     ->setSender($this->getUser())
        //     ->addRecipient($user)
        //     ->setSubject('Hi there')
        //     ->setBody('This is a test message')
        //     ->getMessage();

        return array(
            'form' => $form->createView()
        );
    }
}