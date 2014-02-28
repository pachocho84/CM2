<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
 * @Route("/search")
 */
class SearchController extends Controller
{
    /**
     * @Route("/{query}", name = "search_bar")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function barAction(Request $request, $query)
    {
        $em = $this->getDoctrine()->getManager();

        return array(
            'users' => $em->getRepository('CMBundle:User')->search($query, 5),
            'pages' => $em->getRepository('CMBundle:Page')->search($query, 5),
            'groups' => $em->getRepository('CMBundle:Group')->search($query, 5)
        );
    }
}