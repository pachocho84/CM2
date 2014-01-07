<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
 * @Route("/articles")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/{page}", name = "article_index", requirements={"page" = "\d+"})
     * @Route("/category/{categorySlug}/{page}", name="article_category", requirements={"page" = "\d+"})
     * @Template
     */
    public function indexAction(Request $request, $page = 1, $categorySlug = null)
    {
        $em = $this->getDoctrine()->getManager();
            
        if (!$request->isXmlHttpRequest()) {
            $categories = $em->getRepository('CMBundle:EntityCategory')->getEntityCategories(EntityCategory::ARTICLE, array('locale' => $request->getLocale()));
        }
        
        if ($categorySlug) {
            $category = $em->getRepository('CMBundle:EntityCategory')->getCategory($categorySlug, EntityCategory::ARTICLE, array('locale' => $request->getLocale()));
        }
            
        $articles = $em->getRepository('CMBundle:Article')->getArticles(array(
            'locale' => $request->getLocale(),
            'categoryId'   => $categorySlug ? $category->getId() : null
        ));
        
        $pagination = $this->get('knp_paginator')->paginate($articles, $page, 10);
        
        if ($request->isXmlHttpRequest()) {
            return $this->render('CMBundle:Article:objects.html.twig', array('articles' => $pagination));
        }
        
        return array('categories' => $categories, 'articles' => $pagination, 'category' => $category);
    }

    /**
     * @Route("/{id}/{slug}", name="article_show", requirements={"id" = "\d+", "_locale" = "en|fr|it"})
     * @Template
     */
    public function showAction(Request $request, $id, $slug)
    {
        $em = $this->getDoctrine()->getManager();
            
        if ($request->isXmlHttpRequest()) {
            $date = $em->getRepository('CMBundle:Article')->getDate($id, array('locale' => $request->getLocale()));
            return $this->render('CMBundle:Article:object.html.twig', array('date' => $date));
        }
        
        $article = $em->getRepository('CMBundle:Article')->getArticle($id, array('locale' => $request->getLocale(), 'protagonists' => true));
        $tags = $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()));

        $images = new ArrayCollection();

        $form = $this->createForm(new ImageCollectionType(), $images, array(
                'action' => $this->generateUrl('article_show', array(
                'id' => $article->getId(),
                'slug' => $article->getSlug()
            )),
            'cascade_validation' => true
        ))->add('save', 'submit');

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $article->addImages($images);

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($article);
            $em->flush();

            return new RedirectResponse($this->generateUrl('article_show', array('id' => $article->getId(), 'slug' => $article->getSlug())));
        }

        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $req = $em->getRepository('CMBundle:Request')->getRequestWithUserStatus($this->getUser()->getId(), 'any', array('entityId' => $article->getId()));
        }
        
        return array('article' => $article, 'request' => $req, 'tags' => $tags, 'form' => $form->createView());
    }
    
    /**
     * @Route("/new", name="article_new") 
     * @Route("/{id}/{slug}/edit", name="article_edit", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request, $id = null, $slug = null)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        
        if ($id == null || $slug == null) {
            
            $article = new Article;

            $article->addUser(
                $user,
                true, // admin
                EntityUser::STATUS_ACTIVE,
                true // notifications
            );

            $image = new Image;
            $image->setMain(true)
                ->setUser($user);
            $article->addImage($image);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);

            $article->addPost($post);
        } else {
            $article = $em->getRepository('CMBundle:Article')->getArticle($id, array('locale' => $request->getLocale(), 'protagonists' => true));
            if (!$this->get('cm.user_authentication')->canManage($article)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }
            // TODO: retrieve images from article
        }

        $oldEntityUsers = array();
        foreach ($article->getEntityUsers() as $oldEntityUser) {
            $oldEntityUsers[] = $oldEntityUser;
        }
        
        // TODO: retrieve locales from user

        if ($request->get('_route') == 'article_edit') {
            $formRoute = 'article_edit';
            $formRouteArgs = array('id' => $article->getId(), 'slug' => $article->getSlug());
        } else {
            $formRoute = 'article_new';
            $formRouteArgs = array();
        }
 
        $form = $this->createForm(new ArticleType, $article, array(
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
            foreach ($article->getEntityUsers() as $entityUser) {
                foreach ($oldEntityUsers as $key => $toDel) {
                    if ($toDel->getId() === $entityUser->getId()) {
                        unset($oldEntityUsers[$key]);
                    }
                }
            }

            // remove the relationship between the tag and the Task
            foreach ($oldEntityUsers as $entityUser) {
                // remove the Task from the Tag
                $article->removeEntityUser($entityUser);

                $entityUser->setEntity(null);
                $entityUser->setUser(null);
    
                $em->remove($entityUser);
            }

            $em->persist($article);

            $em->flush();

            // foreach ($article->getEntityUsers() as $entityUser) {
            //     echo $entityUser->getUser().' -> i: '.count($entityUser->getUser()->getRequestsIncoming()).', o: '.'<br/>';
            // }
            // die;

            return new RedirectResponse($this->generateUrl('article_show', array('id' => $article->getId(), 'slug' => $article->getSlug())));
        }

        $users = array();
        foreach ($article->getEntityUsers() as $entityUser) {
            $users[] = $entityUser->getUser();
        }
        
        return array(
            'form' => $form->createView(),
            'entity' => $article,
            'newEntry' => ($formRoute == 'article_new'),
            'joinEntityType' => 'joinArticle'
        );
    }

    /**
     * @Route("/articleDelete/{id}", name="article_delete", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $article = $em->getRepository('CMBundle:Article')->findOneById($id);

        if (!$this->get('cm.user_authentication')->canManage($article)) {
              throw new HttpException(401, 'Unauthorized access.');
        }

        $em->remove($article);
        $em->flush();

        return new JsonResponse(array('title' => $article->getTitle()));
    }
    
    /**
     * @Template
     */
    public function sponsoredAction(Request $request, $limit = 3)
    {
        $request->setLocale($request->get('_locale')); // TODO: workaround for locale in subsession
        
        $sponsored = $this->getDoctrine()->getManager()->getRepository('CMBundle:Article')->getSponsored(array('limit' => $limit, 'locale' => $request->getLocale()));
        
        $pagination  = $this->get('knp_paginator')->paginate($sponsored, 1, $limit);
        
        $this->getDoctrine()->getManager()->createQuery("UPDATE CMBundle:Sponsored s SET s.views = s.views + 1 WHERE s.id IN (2, 20)")->getResult();
        
        return array('sponsored_articles' => $pagination);
    }
}
