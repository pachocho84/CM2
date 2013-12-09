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
use CM\CMBundle\Entity\ImageAlbum;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Form\ImageAlbumType;
use CM\CMBundle\Form\ImageType;

/**
 * @Route("/albums")
 */
class ImageAlbumController extends Controller
{
    
    /**
     * @Route("/new/{object}/{objectId}", name="imagealbum_new", requirements={"objectId" = "\d+"}) 
     * @Route("/{id}/edit/{object}", name="imagealbum_edit", requirements={"id" = "\d+", "objectId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $object = null, $objectId = null, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $page = null;
        $group = null;
        $showRoute = 'user_album';
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
            $album = new ImageAlbum;
            $album->translate('en');
            $album->mergeNewTranslations();
            $album->setType(ImageAlbum::TYPE_ALBUM);

            $image = new Image;
            $image->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $album->addImage($image);

            $post = $this->get('cm.post_center')->newPost(
                $user,
                $user,
                Post::TYPE_CREATION,
                get_class($album),
                array(),
                $album,
                $page,
                $group
            );

            $album->addPost($post);
        } else {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getAlbum($id, array('locale' => $request->getLocale()));
            if (!$this->get('cm.user_authentication')->canManage($album)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
        }
        
        // TODO: retrieve locales from user

        if ($request->get('_route') == 'album_edit') {
            $formRoute = 'album_edit';
            $formRouteArgs = array('id' => $album->getId(), 'slug' => $album->getSlug());
        } else {
            $formRoute = 'album_new';
            $formRouteArgs = array();
        }
 
        $form = $this->createForm(new ImageAlbumType, $album, array(
/*             'action' => $this->generateUrl($formRoute, $formRouteArgs), */
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $user->getRoles(),
            'user_tags' => $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ))->add('save', 'submit');
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($album);

            $em->flush();

            switch ($object) {
                case 'Page':
                    return new RedirectResponse($this->generateUrl('page_album', array('id' => $album->getId(), 'slug' => $page->getSlug())));
                case 'Group':
                    return new RedirectResponse($this->generateUrl('group_album', array('id' => $album->getId(), 'slug' => $group->getSlug())));
                default:
                    return new RedirectResponse($this->generateUrl('user_album', array('id' => $album->getId(), 'slug' => $user->getSlug())));
            }
        }

        $users = array();
        foreach ($album->getEntityUsers() as $entityUser) {
            $users[] = $entityUser->getUser();
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $album,
            'newEntry' => ($formRoute == 'album_new'),
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

    /**
     * @Route("/{slug}/sort/{id}", name="imagealbum_sort", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function sortAction(Request $request, $slug, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));

        try {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getAlbum($id, array('userId' => $user->getId()));
        } catch (\Exception $e) {
            throw new NotFoundHttpException($this->get('translator')->trans('Album not found.', array(), 'http-errors'));
        }

        if (!$this->get('cm.user_authentication')->canManage($album)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }
        
        $images = $em->getRepository('CMBundle:Image')->getImages(array('albumId' => $id, 'paginate' => false, 'limit' => null));
        
        foreach ($images as $image) {
            if ($request->get($image->getId())) {
                $image->setSequence($request->get($image->getId()));
                $em->persist($image);
            }
        }

        $em->flush();

        switch ($request->get('publisher')) {
            case 'User':
                return $this->redirect($this->generateUrl('user_album', array('slug' => $slug, 'id' => $id)), 301);
                break;
            
            default:
                # code...
                break;
        }

        // $this->getUser()->setFlash('success', 'Images successfully sorted.');
        
        // $this->redirect('@image_album_show?id='.$this->album->getEntityId());
    }
}