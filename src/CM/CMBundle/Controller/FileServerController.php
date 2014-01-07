<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Article;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\ArticleDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Sponsored;
use CM\CMBundle\Form\ArticleType;
use CM\CMBundle\Form\ImageCollectionType;
use CM\CMBundle\Utility\UploadHandler;

/**
 * @Route("/file")
 */
class FileServerController extends Controller
{
    /**
     * @Route("/biography/{slug}/{object}", name = "file_biography_pdf", requirements={"userId" = "\d+"})
     * @Template
     */
    public function biographyAction(Request $request, $slug, $object = null)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($object) {
            case 'Page':
                $publisher = $em->getRepository('CMBundle:Page')->findOneById(array('slug' => $slug));
                $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($publisher->getId());
                break;
            case 'Group':
                $publisher = $em->getRepository('CMBundle:Group')->findOneById(array('slug' => $slug));
            $biography = $em->getRepository('CMBundle:Biography')->getGroupBiography($publisher->getId());
                break;
            case 'User':
            default:
                $publisher = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
                $biography = $em->getRepository('CMBundle:Biography')->getUserBiography($publisher->getId());
                break;
        }

        if (count($biography) == 0) {
            $biography = null;
        } else {
            $biography = $biography[0];
        }
        
        if (is_null($publisher)) {
            throw new NotFoundHttpException($this->get('translator')->trans('Publisher not found.', array(), 'http-errors'));
        }

        $html = $this->renderView('CMBundle:FileServer:biography.html.twig', array('publisher' => $publisher, 'biography' => $biography));
        $fileName = $this->container->getParameter('temp.dir').'/'.$publisher->getSlug().'_biography.pdf';
        $this->get('knp_snappy.pdf')->generateFromHtml($html, $fileName, array(), true);

        return new BinaryFileResponse($fileName);
        return new Response($html);
    }
}