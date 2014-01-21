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
use CM\CMBundle\Form\MultimediaType;

/**
 * @Route("/multimedia")
 */
class MultimediaController extends Controller
{
    /**
     * @Route("/{page}", name="multimedia_index", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();
        
        $multimedias = $em->getRepository('CMBundle:Multimedia')->getMultimedias();
        $pagination = $this->get('knp_paginator')->paginate($multimedias, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Multimedia:objects.html.twig', array(
                'group' => $group,
                'multimedias' => $pagination
            ));
        }

        return array(
            'multimedias' => $pagination
        );
    }

    /**
     * @Route("/new/{object}/{objectId}", name="multimedia_new", requirements={"objectId" = "\d+"})
     * @Route("/{id}/{slug}/edit", name="multimedia_edit", requirements={"id" = "\d+"})
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
            $multimedia = new Multimedia;

            $post = $this->get('cm.post_center')->newPost(
                $user,
                $user,
                Post::TYPE_CREATION,
                get_class($multimedia),
                array(),
                $multimedia,
                $page,
                $group
            );

            $multimedia->addPost($post);
        } else {
            $multimedia = $em->getRepository('CMBundle:Multimedia')->getMultimedia($id, array('locale' => $request->getLocale()));
            if (!$this->get('cm.user_authentication')->canManage($multimedia)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }
 
        $form = $this->createForm(new MultimediaType, $multimedia, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $user->getRoles()
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            switch (substr(preg_split('/(www|m)\./', parse_url($multimedia->getLink(), PHP_URL_HOST), null, PREG_SPLIT_NO_EMPTY)[0], 0, 4)) {
                case 'yout':
                    $info = json_decode(file_get_contents($request->getScheme().'://www.youtube.com/oembed?format=json&url='.urlencode($multimedia->getLink())));
                    $multimedia->setType(Multimedia::TYPE_YOUTUBE)
                        ->setLink(preg_replace('/^.*embed\/(.*)\?.*/', '$1', $info->html));
                    $info = json_decode(file_get_contents($request->getScheme().'://gdata.youtube.com/feeds/api/videos/'.$multimedia->getLink().'?v=2&alt=jsonc'))->data;
                    break;
                case 'vime':
                    $info = json_decode(file_get_contents($request->getScheme().'://vimeo.com/api/oembed.json?url='.urlencode($multimedia->getLink())));
                    $multimedia->setType(Multimedia::TYPE_VIMEO)
                        ->setLink($info->video_id);
                    break;
                case 'soun':
                    $info = json_decode(file_get_contents($request->getScheme().'://soundcloud.com/oembed.json?url='.urlencode($multimedia->getLink())));
                    $multimedia->setType(Multimedia::TYPE_SOUNDCLOUD)
                        ->setLink(preg_replace('/^.*tracks%2F(.*)&.*/', '$1', $info->html));
                    break;
            }

            $multimedia->setTitle($info->title)
                ->setText($info->description);

            $em->persist($multimedia);

            $em->flush();

            return new RedirectResponse($this->generateUrl($multimedia->getPost()->getPublisherRoute().'_multimedia_show', array('id' => $multimedia->getId(), 'slug' => $multimedia->getPost()->getPublisher()->getSlug())));
        }

        $users = array();
        foreach ($multimedia->getEntityUsers() as $entityUser) {
            $users[] = $entityUser->getUser();
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $multimedia,
            'joinEntityType' => 'joinalbum'
        );
    }

    /**
     * @Route("/multimedia/{id}/add", name="multimediaalbum_add_multimedia", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:MultimediaAlbum:singleMultimedia.html.twig")
     */
    public function addMultimediaAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $album = $em->getRepository('CMBundle:MultimediaAlbum')->getAlbum($id, array('locale' => $request->getLocale()));

        if (!$this->get('cm.user_authentication')->canManage($album)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $multimedia = new Multimedia;
        $multimedia->setType($type);
        $multimedia->setLink($link);
        $multimedia->setTitle($info->title)
            ->setText($info->description);;
        if (!is_null($album->getPost()->getPage())) {
            $multimedia->setPage($album->getPost()->getPage());
            $publisher = $album->getPost()->getPage();
            $link = 'page_multimedia';
        } elseif (!is_null($album->getPost()->getGroup())) {
            $multimedia->setGroup($album->getPost()->getGroup());
            $publisher = $album->getPost()->getGroup();
            $link = 'group_multimedia';
        } else {
            $publisher = $this->getUser();
            $link = 'user_multimedia';
        }

        foreach ($request->files as $file) {
            $multimedia->setImgFile($file);
        }

        $errors = $this->get('validator')->validate($multimedia);

        if (count($errors) > 0) {
            throw new HttpException(403, $this->get('translator')->trans('Error in file.', array(), 'http-errors'));
        }

        $em->persist($album);
        $em->flush();

        return array(
            'multimedia' => $multimedia,
            'link' => $link,
            'publisher' => $publisher
        );
    }
    
    /**
     * @Route("/{id}/{slug}", name="multimedia_show", requirements={"id" = "\d+"})
     * @Template
     */
    public function showAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
            
        // if ($request->isXmlHttpRequest()) {
        //     $date = $em->getRepository('CMBundle:Multimedia')->getDate($id, array('locale' => $request->getLocale()));
        //     return $this->render('CMBundle:Multimedia:object.html.twig', array('date' => $date));
        // }
        
        $multimedia = $em->getRepository('CMBundle:Multimedia')->getMultimedia($id, array('locale' => $request->getLocale()));
        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Multimedia:object.html.twig', array('multimedia' => $multimedia));
        }
        
        return array('multimedia' => $multimedia);
    }
}