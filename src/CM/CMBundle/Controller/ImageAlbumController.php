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

class ImageAlbumController extends Controller
{
    /**
     * @Route("/albums/new/{object}/{objectId}", name="imagealbum_new", requirements={"objectId" = "\d+"}) 
     * @Route("/{id}/edit/{object}", name="imagealbum_edit", requirements={"id" = "\d+", "objectId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $object = null, $objectId = null, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $publisher = $this->getUser();
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
                ->setUser($publisher)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $post = $this->get('cm.post_center')->newPost(
                $publisher,
                $publisher,
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
            'roles' => $publisher->getRoles(),
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
                    return new RedirectResponse($this->generateUrl('user_album', array('id' => $album->getId(), 'slug' => $publisher->getSlug())));
            }
        }

        $publishers = array();
        foreach ($album->getEntityUsers() as $entityUser) {
            $publishers[] = $entityUser->getUser();
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $album,
            'newEntry' => ($formRoute == 'album_new'),
            'joinEntityType' => 'joinalbum'
        );
    }

    /**
     * @Route("/albums/image/{id}/add", name="imagealbum_add_image", requirements={"id" = "\d+"})
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
     * @Route("/albums/{slug}/sort/{id}", name="imagealbum_sort", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function sortAction(Request $request, $slug, $id)
    {
        $em = $this->getDoctrine()->getManager();

        try {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getAlbum($id);
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
            case 'events':
                return $this->redirect($this->generateUrl('entity_album', array('type' => $request->get('publisher'), 'id' => $id, 'slug' => $slug)), 301);
                break;
            
            default:
                # code...
                break;
        }

        // $this->getUser()->setFlash('success', 'Images successfully sorted.');
        
        // $this->redirect('@image_album_show?id='.$this->album->getEntityId());
    }

    /**
     * @Route("/albums/main/{id}", name="imagealbum_main", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function makeMainAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository('CMBundle:Image')->findOneById($id);
        
        if (!$image) {
            throw new NotFoundHttpException($this->get('translator')->trans('Image not found.', array(), 'http-errors'));
        }

        if (!$this->get('cm.user_authentication')->canManage($image) || ($image->getEntity() instanceof ImageAlbum && $image->getEntity()->getType() != ImageAlbum::TYPE_ALBUM)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }
        
        $em->getRepository('CMBundle:ImageAlbum')->setMain($id, $image->getEntityId());

        return new Response;
    }

    protected function countAlbumsAndImages($options)
    {
        return $this->getDoctrine()->getManager()->getRepository('CMBundle:ImageAlbum')->countAlbumsAndImages($options);
    }

    /**
     * @Route("/{slug}/images/{page}", name="user_images", requirements={"page" = "\d+"})
     * @Route("/pages/{slug}/images/{page}", name="page_images", requirements={"page" = "\d+"})
     * @Route("/groups/{slug}/images/{page}", name="group_images", requirements={"page" = "\d+"})
     */
    public function imagesAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('_route') == 'user_images') {
            $publisher = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }

            $publisherType = 'user';

            $template = 'CMBundle:User:images.html.twig';
        } elseif ($request->get('_route') == 'page_images') {
            $publisher = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('Page not found.', array(), 'http-errors'));
            }

            $publisherType = 'page';

            $template = 'CMBundle:Page:images.html.twig';
        } elseif ($request->get('_route') == 'group_images') {
            $publisher = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
            }

            $publisherType = 'group';

            $template = 'CMBundle:Group:images.html.twig';
        }

        $images = $em->getRepository('CMBundle:Image')->getImages(array($publisherType.'Id' => $publisher->getId()));
        
        $pagination = $this->get('knp_paginator')->paginate($images, $page, 40);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:imageList.html.twig', array(
                $publisherType => $publisher,
                'images' => $pagination,
                'link' => $publisherType.'_image',
                'publisher' => $publisher
            ));
        }

        return new Response($this->renderView($template, array(
            $publisherType => $publisher,
            'images' => $pagination,
            'count' => $this->countAlbumsAndImages(array($publisherType.'Id' => $publisher->getId()))
        )));
    }

    /**
     * @Route("/image/{id}", name="image_show", requirements={"id" = "\d+"})
     * @Template
     */
    public function imageAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->isXmlHttpRequest()) {
            $image = $em->getRepository('CMBundle:Image')->getImage($id, array('locale' => $request->getLocale()));
            
            if (is_null($image)) {
                throw new NotFoundHttpException($this->get('translator')->trans('Image not found.', array(), 'http-errors'));
            }

            return new JsonResponse(array(
                'albumTitle' => $image->getEntity()->getTitle(),
                'sidebar' => $this->renderView('CMBundle:Wall:sidebarSocial.html.twig', array('post' => $image, 'isImage' => true))
            ));
        }

        $image = $em->getRepository('CMBundle:Image')->getImage($id, array('locale' => $request->getLocale()));
        
        if (is_null($image)) {
            throw new NotFoundHttpException($this->get('translator')->trans('Image not found.', array(), 'http-errors'));
        }

        $imageIdsInAlbum = $em->getRepository('CMBundle:ImageAlbum')->getImagesDataInAlbum($image->getEntityId());
        
        $index = array_search(array('id' => $id), $imageIdsInAlbum);
        $prev = array_key_exists($index - 1, $imageIdsInAlbum) ? $imageIdsInAlbum[$index - 1]['id'] : $imageIdsInAlbum[count($imageIdsInAlbum) - 1]['id'];
        $next = array_key_exists($index + 1, $imageIdsInAlbum) ? $imageIdsInAlbum[$index + 1]['id'] : $imageIdsInAlbum[0]['id'];

        return array(
            'image' => $image,
            'prevId' => $prev,
            'nextId' => $next,
            // 'count' => $this->countAlbumsAndImages(array($image->getPublisherType().'Id' => $image->getPublisher()->getId()))
        );
    }

    /**
     * @Route("/images/data/{id}", name="images_data", requirements={"id" = "\d+"})
     * @Route("/images/data/{type}/{publisherId}/{id}", name="image_data_publisher", requirements={"id" = "\d+", "publisherId" = "\d+"})
     */
    public function imagesDataAction(Request $request, $id, $slug = null, $type = null, $publisherId = null)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('_route') == 'images_data') {
            $image = $em->getRepository('CMBundle:Image')->findOneById($id);
            
            if (is_null($image)) {
                throw new NotFoundHttpException($this->get('translator')->trans('Image not found.', array(), 'http-errors'));
            }
            $imagesDataInAlbum = $em->getRepository('CMBundle:ImageAlbum')->getImagesDataInAlbum($image->getEntityId(), 'id, img, imgOffset');
        } else {
            $imagesDataInAlbum = $em->getRepository('CMBundle:Image')->getImages(array($type.'Id' => $publisherId));
            $imagesDataInAlbum = $this->get('knp_paginator')->paginate($imagesDataInAlbum, 1, 40);
            $imagesDataInAlbum = $imagesDataInAlbum->getItems();
            $imagesDataInAlbum = $em->getRepository('CMBundle:ImageAlbum')->getImagesDataPerPublisher($type, $publisherId, 'id, img, imgOffset');
        }

        return new JsonResponse(array(
            'id' => $id,
            'images' => $imagesDataInAlbum
        ));
    }

    /**
     * @Route("/{slug}/albums/{page}", name="user_albums", requirements={"page" = "\d+"})
     * @Route("/pages/{slug}/albums/{page}", name="page_albums", requirements={"page" = "\d+"})
     * @Route("/groups/{slug}/albums/{page}", name="group_albums", requirements={"page" = "\d+"})
     */
    public function albumsAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('_route') == 'user_albums') {
            $publisher = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
            
            $publisherType = 'user';

            $template = 'CMBundle:User:albums.html.twig';
        } elseif ($request->get('_route') == 'page_albums') {
            $publisher = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('Page not found.', array(), 'http-errors'));
            }

            $publisherType = 'page';

            $template = 'CMBundle:Page:albums.html.twig';
        } elseif ($request->get('_route') == 'group_albums') {
            $publisher = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
            }

            $publisherType = 'group';

            $template = 'CMBundle:Group:albums.html.twig';
        }

        $albums = $em->getRepository('CMBundle:ImageAlbum')->getAlbums(array(
            $publisherType.'Id' => $publisher->getId(),
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($albums, $page, 40);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:albumList.html.twig', array(
                $publisherType => $publisher,
                'albums' => $pagination,
                'link' => $publisherType.'_album',
                'publisher' => $publisher
            ));
        }

        return new Response($this->renderView($template, array(
            $publisherType => $publisher,
            'albums' => $pagination,
            'count' => $this->countAlbumsAndImages(array($publisherType.'Id' => $publisher->getId()))
        )));
    }

    /**
     * @Route("/{type}/{id}/{slug}/album/{page}", name="entity_album", requirements={"id" = "\d+", "page" = "\d+"})
     * @Route("/{slug}/album/{id}/{page}", name="user_album", requirements={"id" = "\d+", "page" = "\d+"})
     * @Route("/pages/{slug}/album/{id}/{page}", name="page_album", requirements={"id" = "\d+", "page" = "\d+"})
     * @Route("/groups/{slug}/album/{id}/{page}", name="group_album", requirements={"id" = "\d+", "page" = "\d+"})
     */
    public function albumAction(Request $request, $id, $type = null, $slug = null, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $album = $em->getRepository('CMBundle:ImageAlbum')->getAlbum($id, array('entityType' => $type, 'slug' => $slug));
        
        if (is_null($album) || $album->getPost()->getPublisher()->getSlug() == $slug) {
            throw new NotFoundHttpException($this->get('translator')->trans('Album not found.', array(), 'http-errors'));
        }

        $images = $em->getRepository('CMBundle:Image')->getImages(array('albumId' => $id));
        
        $pagination = $this->get('knp_paginator')->paginate($images, $page, 40);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:imageList.html.twig', array(
                strtolower($album->getPost()->getPublisher()->className()) => $album->getPost()->getPublisher(),
                'album' => $album,
                'images' => $pagination
            ));
        }

        $publisherType = strtolower($album->getPost()->getPublisher()->className());
        return new Response($this->renderView('CMBundle:'.ucfirst(substr($request->get('_route'), 0, -6)).':album.html.twig', array(
            $publisherType => $album->getPost()->getPublisher(),
            'album' => $album,
            'images' => $pagination,
            'count' => $this->countAlbumsAndImages(array($publisherType.'Id' => $album->getPost()->getPublisher()))
        )));
    }

    /**
     * @Route("/{slug}/images/entities/{page}", name="user_entities_albums", requirements={"page" = "\d+"})
     * @Route("/pages/{slug}/images/entities/{page}", name="page_entities_albums", requirements={"page" = "\d+"})
     * @Route("/groups/{slug}/images/entities/{page}", name="group_entities_albums", requirements={"page" = "\d+"})
     */
    public function imagesEntitiesAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        if ($request->get('_route') == 'user_entities_albums') {
            $publisher = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
            
            $publisherType = 'user';

            $template = 'CMBundle:User:imagesEntities.html.twig';
        } elseif ($request->get('_route') == 'page_entities_albums') {
            $publisher = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('Page not found.', array(), 'http-errors'));
            }

            $publisherType = 'page';

            $template = 'CMBundle:Page:imagesEntities.html.twig';
        } elseif ($request->get('_route') == 'group_entities_albums') {
            $publisher = $em->getRepository('CMBundle:Group')->findOneBy(array('slug' => $slug));
            
            if (!$publisher) {
                throw new NotFoundHttpException($this->get('translator')->trans('Group not found.', array(), 'http-errors'));
            }

            $publisherType = 'group';

            $template = 'CMBundle:Group:imagesEntities.html.twig';
        }

        $entities = $em->getRepository('CMBundle:Image')->getEntityImages(array(
            $publisherType.'Id' => $publisher->getId(),
            // 'paginate' => false
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($entities, $page, 40);

        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:ImageAlbum:imageEntityList.html.twig', array(
                $publisherType => $publisher,
                'entities' => $pagination,
                'link' => $publisherType.'_album',
                'publisher' => $publisher
            ));
        }

        return new Response($this->renderView($template, array(
            $publisherType => $publisher,
            'entities' => $pagination,
            'count' => $this->countAlbumsAndImages(array($publisherType.'Id' => $publisher->getId()))
        )));
    }
}