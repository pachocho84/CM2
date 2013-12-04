<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
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

/**
 * @Route("/albums")
 */
class ImageAlbumController extends Controller
{
    
    /**
     * @Route("/new/{object}/{objectId}", name="imagealbum_new", requirements={"objectId" = "\d+"}) 
     * @Route("/{id}/{slug}/edit/{object}/{objectId}", name="imagealbum_edit", requirements={"id" = "\d+", "objectId" = "\d+"})
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
        if (!is_null($object)) {
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
            $album = $em->getRepository('CMBundle:album')->getAlbum($id, array('locale' => $request->getLocale()));
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
                    return new RedirectResponse($this->generateUrl('page_album', array('id' => $album->getId(), 'slug' => $user->getSlug())));
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
}