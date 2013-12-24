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
use CM\CMBundle\Entity\Link;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Form\LinkType;

/**
 * @Route("/links")
 */
class LinkController extends Controller
{
    /**
     * @Route("/{page}", name="link_index", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $links = $em->getRepository('CMBundle:Link')->getLinks();
        $pagination = $this->get('knp_paginator')->paginate($links, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Link:objects.html.twig', array(
                'group' => $group,
                'links' => $pagination
            ));
        }

        return array(
            'links' => $pagination
        );
    }

    /**
     * @Route("/new/{object}/{objectId}", name="link_new", requirements={"objectId" = "\d+"})
     * @Route("/{id}/{slug}/edit", name="link_edit", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $object = null, $objectId = null, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $page = null;
        $group = null;
        if (!is_null($objectId)) {
            switch ($object) {
                case 'Page':
                    $page = $em->getRepository('CMBundle:Page')->findOneById($objectId);
                    if (!$this->get('cm.user_authentication')->isAdminOf($page)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }
                    break;
                case 'Group':
                    $group = $em->getRepository('CMBundle:Group')->findOneById($objectId);
                    if (!$this->get('cm.user_authentication')->isAdminOf($group)) {
                        throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
                    }
                    break;
            }
            if (is_null($page) && is_null($group)) {
                throw new NotFoundHttpException($this->get('translator')->trans('Object not found.', array(), 'http-errors'));
            }
        }
        
        if (is_null($id)) {
            $link = new Link;

            $post = $this->get('cm.post_center')->newPost(
                $user,
                $user,
                Post::TYPE_CREATION,
                get_class($link),
                array(),
                $link,
                $page,
                $group
            );

            $link->addPost($post);
        } else {
            $link = $em->getRepository('CMBundle:Link')->getLink($id, array('locale' => $request->getLocale()));
            if (!$this->get('cm.user_authentication')->canManage($link)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }
 
        $form = $this->createForm(new LinkType, $link, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $user->getRoles()
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {

            switch (substr(preg_split('/(www|m)\./', parse_url($link->getLink(), PHP_URL_HOST), null, PREG_SPLIT_NO_EMPTY)[0], 0, 4)) {
                case 'yout':
                    $info = json_decode(file_get_contents($request->getScheme().'://www.youtube.com/oembed?format=json&url='.urlencode($link->getLink())));
                    $link->setType(Link::TYPE_YOUTUBE)
                        ->setLink(preg_replace('/^.*embed\/(.*)\?.*/', '$1', $info->html));
                    $info = json_decode(file_get_contents($request->getScheme().'://gdata.youtube.com/feeds/api/videos/'.$link->getLink().'?v=2&alt=jsonc'))->data;
                    break;
                case 'vime':
                    $info = json_decode(file_get_contents($request->getScheme().'://vimeo.com/api/oembed.json?url='.urlencode($link->getLink())));
                    $link->setType(Link::TYPE_VIMEO)
                        ->setLink($info->video_id);
                    break;
                case 'soun':
                    $info = json_decode(file_get_contents($request->getScheme().'://soundcloud.com/oembed.json?url='.urlencode($link->getLink())));
                    $link->setType(Link::TYPE_SOUNDCLOUD)
                        ->setLink(preg_replace('/^.*tracks%2F(.*)&.*/', '$1', $info->html));
                    break;
            }

            $link->setTitle($info->title)
                ->setText($info->description);

            $em->persist($link);

            $em->flush();

            return new RedirectResponse($this->generateUrl($link->getPost()->getPublisherRoute().'_link_show', array('id' => $link->getId(), 'slug' => $link->getPost()->getPublisher()->getSlug())));
        }

        $users = array();
        foreach ($link->getEntityUsers() as $entityUser) {
            $users[] = $entityUser->getUser();
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $link,
            'joinEntityType' => 'joinalbum'
        );
    }
    
    /**
     * @Route("/{id}/{slug}", name="link_show", requirements={"id" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
            
        // if ($request->isXmlHttpRequest()) {
        //     $date = $em->getRepository('CMBundle:Link')->getDate($id, array('locale' => $request->getLocale()));
        //     return $this->render('CMBundle:Link:object.html.twig', array('date' => $date));
        // }
        
        $link = $em->getRepository('CMBundle:Link')->getLink($id, array('locale' => $request->getLocale()));
        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Link:object.html.twig', array('link' => $link));
        }
        
        return array('link' => $link);
    }
}