<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\Education;
use CM\CMBundle\Entity\Tag;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\UserImageType;
use CM\CMBundle\Form\UserTagsType;
use CM\CMBundle\Form\EducationType;
use CM\CMBundle\Form\PageUserCollectionType;

class NotificationController extends Controller
{
    /**
     * @Route("/notifications/{page}/{perPage}", name="notification_show", requirements={"page" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function showAction(Request $request, $page = 1, $perPage = 6)
    {
        $em = $this->getDoctrine()->getManager();

        $notifications = $em->getRepository('CMBundle:Notification')->getNotifications($this->getUser()->getId());
        $pagination = $this->get('knp_paginator')->paginate($notifications, $page, $perPage);

        $this->get('cm.notification_center')->seeNotifications($this->getUser()->getId());

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Notification:objects.html.twig', array('notifications' => $pagination));
        }

        return array('notifications' => $pagination);
    }
}
