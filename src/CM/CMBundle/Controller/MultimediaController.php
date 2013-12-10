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
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Form\MultimediaType;

/**
 * @Route("/multimedia")
 */
class MultimediaController extends Controller
{
    /**
     * @Route("/new/{object}/{objectId}", name="imagealbum_new", requirements={"objectId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function newAction(Request $request, $object = null, $objectId = null, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $page = null;
        $group = null;
        if (!is_null($objectId)) {
            switch ($object) {
                case 'Page':
                    $page = $em->getRepository('CMBundle:Page')->findOneById($objectId);
                    break;
                case 'Group':
                    $group = $em->getRepository('CMBundle:Group')->findOneById($objectId);
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
     * @Route("/addImage/{id}", name="imagealbum_add_image", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:ImageAlbum:singleImage.html.twig")
     */
    public function addImageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $album = $em->getRepository('CMBundle:ImageAlbum')->getAlbum($id, array('locale' => $request->getLocale()));

        if (!$this->get('cm.user_authentication')->canManage($album)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $image = new Image;
        $image->setMain(false)
            ->setUser($this->getUser());
        if (!is_null($album->getPost()->getPage())) {
            $image->setPage($album->getPost()->getPage());
            $publisher = $album->getPost()->getPage();
            $link = 'page_image';
        } elseif (!is_null($album->getPost()->getGroup())) {
            $image->setGroup($album->getPost()->getGroup());
            $publisher = $album->getPost()->getGroup();
            $link = 'group_image';
        } else {
            $publisher = $this->getUser();
            $link = 'user_image';
        }

        foreach ($request->files as $file) {
            $image->setImgFile($file);
        }

        $errors = $this->get('validator')->validate($image);

        if (count($errors) > 0) {
            throw new HttpException(403, $this->get('translator')->trans('Error in file.', array(), 'http-errors'));
        }
            
        $album->addImage($image);

        $em->persist($album);
        $em->flush();

        return array(
            'album' => $album,
            'image' => $image,
            'link' => $link,
            'publisher' => $publisher
        );
    }
}