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
     * @Route("", name = "search_bar")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function barAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $request->get('q');

        $users = $em->getRepository('CMBundle:User')->search($query, 5);
        $pages = $em->getRepository('CMBundle:Page')->search($query, 5);
        $groups = $em->getRepository('CMBundle:Group')->search($query, 5);

        if ($request->isXmlHttpRequest()) {
            $results = array();

            foreach($users as $user)
            {
                $view['id'] = $user->getId();
                $view['url'] = $this->generateUrl('user_show', array('slug' => $user->getSlug()));
                $view['label'] = $user->__toString();
                $view['view'] = $this->renderView('CMBundle:Search:user.html.twig', array('user' => $user));
                $results[] = $view;
            }

            foreach($pages as $page)
            {
                $view['id'] = $page->getId();
                $view['url'] = $this->generateUrl('page_show', array('slug' => $page->getSlug()));
                $view['label'] = $page->__toString();
                $view['view'] = $this->renderView('CMBundle:Search:page.html.twig', array('page' => $page));
                $results[] = $view;
            }

            foreach($groups as $group)
            {
                $view['id'] = $group->getId();
                $view['url'] = $this->generateUrl('group_show', array('slug' => $group->getSlug()));
                $view['label'] = $group->__toString();
                $view['view'] = $this->renderView('CMBundle:Search:group.html.twig', array('group' => $group));
                $results[] = $view;
            }

            return new JsonResponse($results);
        }

        return array(
            'users' => $users,
            'pages' => $pages,
            'groups' => $groups
        );
    }
}