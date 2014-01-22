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
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\ImageAlbum;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Route("/images")
 */
class ImageController extends Controller
{
    /**
     * @Route("/entity/{type}/{id}", name="image_entity", requirements={"id" = "\d+"}) 
     * @Template
     */
    public function entityAction(Request $request, $id, $type)
    {
        return array(
            'entityId' => $id,
            'entityType' => $type,
            'images' => $this->getDoctrine()->getManager()->getRepository('CMBundle:Image')->findBy(array('entityId' => $id), array('main' => 'desc', 'sequence' => 'asc'), 5)
        );
    }
    
    /**
     * @Route("/makeProfile/{id}", name="image_make_profile", requirements={"id" = "\d+"}) 
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function profileAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository('CMBundle:Image')->findOneById($id);

        $extension = pathinfo($image->getImg())['extension'];
        $fileName = md5(uniqid().$image->getImg().time()).'.'.$extension;
        copy($image->getImgAbsolutePath(), $image->getUploadRootDir().'/'.$fileName);

        $user = $this->getUser();
        $user->setImg($fileName);

        $em->persist($user);
        $em->flush();
        
        return new Response($image->getImgAbsolutePath().' '.$image->getUploadRootDir().'/'.$fileName.' '.$user->getImg());
    }

    /**
     * @Route("/makeCover/{id}", name="image_make_cover", requirements={"id" = "\d+"}) 
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function coverAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $image = $em->getRepository('CMBundle:Image')->findOneById($id);

        $extension = pathinfo($image->getImg())['extension'];
        $fileName = md5(uniqid().$image->getImg().time()).'.'.$extension;
        copy($image->getImgAbsolutePath(), $image->getUploadRootDir().'/'.$fileName);

        $user = $this->getUser();
        $user->setCoverImg($fileName);

        $em->persist($user);
        $em->flush();
        
        return new Response($image->getImgAbsolutePath().' '.$image->getUploadRootDir().'/'.$fileName.' '.$user->getImg());
    }
}