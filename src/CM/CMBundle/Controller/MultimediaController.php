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
        
        $multimedia = $em->getRepository('CMBundle:Multimedia')->getMultimediaList();
        $pagination = $this->get('knp_paginator')->paginate($multimedia, $page, 10);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Multimedia:objects.html.twig', array(
                'group' => $group,
                'multimediaList' => $pagination
            ));
        }

        return array(
            'multimediaList' => $pagination
        );
    }

    /**
     * @Route("/new", name="multimedia_new")
     * @Route("/{id}/{slug}/edit", name="multimedia_edit", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (is_null($id)) {
            $multimedia = new Multimedia;

            $multimedia->addUser(
                $this->getUser(),
                true, // admin
                EntityUser::STATUS_ACTIVE,
                true // notifications
            );

            $post = $this->get('cm.post_center')->newPost(
                $this->getUser(),
                $this->getUser(),
                Post::TYPE_CREATION,
                get_class($multimedia),
                array(),
                $multimedia
            );

            $multimedia->addPost($post);
        } else {
            $multimedia = $em->getRepository('CMBundle:Multimedia')->getMultimedia($id, array('locale' => $request->getLocale()));
            if (!$this->get('cm.user_authentication')->canManage($multimedia)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }

        $oldEntityUsers = array();
        foreach ($multimedia->getEntityUsers() as $oldEntityUser) {
            $oldEntityUsers[] = $oldEntityUser;
        }
 
        $form = $this->createForm(new MultimediaType, $multimedia, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $this->getUser()->getRoles()
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

            foreach ($multimedia->getEntityUsers() as $entityUser) {
                foreach ($oldEntityUsers as $key => $toDel) {
                    if ($toDel->getId() === $entityUser->getId()) {
                        unset($oldEntityUsers[$key]);
                    }
                }
            }

            // remove the relationship between the tag and the Task
            foreach ($oldEntityUsers as $entityUser) {
                // remove the Task from the Tag
                $multimedia->removeEntityUser($entityUser);

                $entityUser->setEntity(null);
                $entityUser->setUser(null);
    
                $em->remove($entityUser);
            }

            $em->persist($multimedia);

            $em->flush();

            return new RedirectResponse($this->generateUrl('multimedia_show', array('id' => $multimedia->getId(), 'slug' => $multimedia->getSlug())));
        }

        $users = array();
        foreach ($multimedia->getEntityUsers() as $entityUser) {
            $users[] = $entityUser->getUser();
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $multimedia,
            'newEntry' => ($formRoute == 'multimedia_new'),
            'joinEntityType' => 'joinalbum'
        );
    }
}