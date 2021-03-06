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
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\BiographyType;
use CM\CMBundle\Form\UserImageType;

class BiographyController extends Controller
{
    /**
     * @Route("/account/biography", name="user_biography_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function userEditAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($user->getId());
        if (is_null($biography) || !$biography) {
            $biography = new Biography;

            $post = $this->get('cm.post_center')->getNewPost($user, $user);

            $biography->setPost($post);
        }
 
        $form = $this->createForm(new BiographyType, $biography, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
            'error_bubbling' => false,
            'roles' => $this->getUser()->getRoles(),
            'em' => $em,
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ));
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $biography->setTitle('b');
            $em->persist($biography);
            $em->flush();

            return new RedirectResponse($this->generateUrl('user_biography', array('slug' => $this->getUser()->getSlug())));
        }
        
        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{slug}/biography", name="user_biography")
     * @Template
     */
    public function userAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }

        $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($user->getId(), array('locale' => $request->getLocale()));

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Biography:box.html.twig', array(
                'publisher' => $user,
                'publisherType' => 'user',
                'biography' => $biography,
                'simple' => $request->get('simple')
            ));
        }

        $lastWork = $em->getRepository('CMBundle:Work')->getLast($user->getId());
        $lastEducation = $em->getRepository('CMBundle:Education')->getLast($user->getId());

        return array(
            'user' => $user,
            'biography' => $biography,
            'lastWork' => $lastWork,
            'lastEducation' => $lastEducation,
        );
    }

    /**
     * @Route("/pages/{slug}/account/biography", name="page_biography_edit")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function pageEditAction(Request $request, $slug)
    {
        // $em = $this->getDoctrine()->getManager();
        
        // $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        // if (!$page) {
        //     throw new NotFoundHttpException('Page not found.');
        // }

        // $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($page->getId());
        // if (is_null($biography) || !$biography) {
        //     $biography = new Biography;

        //     $post = $this->get('cm.post_center')->getNewPost($this->getUser(), $this->getUser());
        //     $post->setPage($page);

        //     $biography->addPost($post);
        // } else {
        //     $biography = $biography[0];
        // }
 
        // $form = $this->createForm(new BiographyType, $biography, array(
        //     'cascade_validation' => true,
        //     'error_bubbling' => false,
        //     'roles' => $this->getUser()->getRoles(),
        //     'em' => $em,
        //     'locales' => array('en'/* , 'fr', 'it' */),
        //     'locale' => $request->getLocale()
        // ))->add('save', 'submit');
        
        // $form->handleRequest($request);

        // if ($form->isValid()) {
        //     $biography->setTitle('b');
        //     $em->persist($biography);
        //     $em->flush();

        //     return new RedirectResponse($this->generateUrl('page_biography', array('slug' => $page->getSlug())));
        // }
        
        // return array(
        //     'form' => $form->createView()
        // );
    }

    /**
     * @Route("/pages/{slug}/biography", name="page_biography")
     * @Template
     */
    public function pageAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($page->getId(), array('locale' => $request->getLocale()));

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Biography:box.html.twig', array(
                'publisher' => $page,
                'publisherType' => 'page',
                'biography' => $biography,
                'simple' => $request->get('simple')
            ));
        }

        return array('page' => $page, 'biography' => $biography);
    }
}
