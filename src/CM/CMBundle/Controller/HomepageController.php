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
use CM\CMBundle\Entity\HomepageArchive;
use CM\CMBundle\Entity\HomepageBox;
use CM\CMBundle\Entity\HomepageCategory;
use CM\CMBundle\Entity\HomepageCategoryTranslation;
use CM\CMBundle\Entity\HomepageColumn;
use CM\CMBundle\Entity\HomepageRow;
use CM\CMBundle\Form\ArticleType;
use CM\CMBundle\Form\ImageCollectionType;
use CM\CMBundle\Utility\UploadHandler;


use Symfony\Component\HttpKernel\Controller\ControllerReference;

class HomepageController extends Controller
{
    /**
     * @Route("/{page}", name="homepage_index", requirements={"page"="\d+"})
     * @Route("/vips/{page}", name="homepage_vips")
     * @Route("/fans/{page}", name="homepage_fans")
     * @Route("/connections/{page}", name="homepage_connections")
     * @Route("/editorial/{page}", name="homepage_editorial")
     * @Template
     */
    public function indexAction(Request $request, $page = 1)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();

            $boxes = array();

            /* Last registered users */
/*             $boxes['lastUsers;left'] = $this->renderView('CMBundle:Homepage:lastUsers.html.twig', array('lastUsers' => $em->getRepository('CMBundle:User')->getLastRegisteredUsers(28))); */

            /* Login/Register box */
/*
            if (!$this->get('security.context')->isGranted('ROLE_USER')) {
                $boxes['login_register;right'] = $this->renderView('CMBundle:Homepage:boxAuthentication.html.twig');
            }
*/

            /* Next events */
            if ($request->get('_route') == 'homepage_index') {
                $dates = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Event')->getNextDates(array('locale' => $request->getLocale())), $page, 3);
                $boxes['dates;right'] = $this->renderView('CMBundle:Homepage:boxEvents.html.twig', array('dates' => $dates));
            }

            /* Sponsored */
            $sponsoreds = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Sponsored')->getLessViewed(array('locale' => $request->getLocale())), $page, 2);
            foreach ($sponsoreds as $sponsored) {
                $boxes['sponsored_'.$sponsored->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $sponsored->getEntity()->getPost(), 'postType' => 'sponsored'));
            }

            /* Box partners */
            if ($request->get('_route') == 'homepage_index') {
                $homepageBoxes = $em->getRepository('CMBundle:HomepageBox')->getBoxes(4, array('locale' => $request->getLocale()));
                foreach ($homepageBoxes as $box) {
                    switch ($box->getType()) {
                        case HomepageBox::TYPE_EVENT:
                            $objects = $em->getRepository('CMBundle:Event')->getNextDates(array('pageId' => $box->getPageId(), 'locale' => $request->getLocale()));
                            $limit = 5;
                            break;
                        case HomepageBox::TYPE_DISC:
                            $objects = $em->getRepository('CMBundle:Disc')->getDiscs(array('pageId' => $box->getPageId(), 'locale' => $request->getLocale()));
                            $limit = 5;
                            break;
                        case HomepageBox::TYPE_ARTICLE:
                            $objects = $em->getRepository('CMBundle:Disc')->getArticles(array('pageId' => $box->getPageId(), 'locale' => $request->getLocale()));
                            $limit = 5;
                            break;
                        case HomepageBox::TYPE_RUBRIC:
                            $objects = $em->getRepository('CMBundle:HomepageArchive')->getArticles($box->getCategoryId(), array('locale' => $request->getLocale()));
                            $limit = 3;
                            break;
                    }
                    $objects = $this->get('knp_paginator')->paginate($objects, $page, $limit);

                    if (empty($objects->getItems()) && $box->getType() != HomepageBox::TYPE_RUBRIC) {
                        $biography = $em->getRepository('CMBundle:Biography')->getPageBiography($box->getPageId(), array('locale' => $request->getLocale()));
                    }

                    $boxes['homepage_'.$box->getPosition()] = $this->renderView('CMBundle:Homepage:boxPartner.html.twig', array('box' => $box, 'objects' => $objects, 'biography' => $biography));
                }
            }

            /* Vips */
            if (in_array($request->get('_route'), array('homepage_index', 'homepage_vips'))) {
                $vips = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('vip' => true, 'entityCreation' => true, 'locale' => $request->getLocale())), $page, 2);
                foreach ($vips as $post) {
                    $boxes['vip_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post, 'postType' => 'vip'));
                }
            }

            /* Reviews */
/*
            if (in_array($request->get('_route'), array('homepage_index', 'homepage_newspaper'))) {
                if ($page == 1) {
                    $reviews = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:HomepageArchive')->getLastReviews(array('locale' => $request->getLocale())), $page, 4);
                    $boxes['reviews'] = $this->renderView('CMBundle:Homepage:boxReviews.html.twig', array('reviews' => $reviews));
                }
            }
*/

            /* Banners */
/*
            $banners = $em->getRepository('CMBundle:HomepageBanner')->getBanners(($page -1) * 2, 3);
            foreach ($banners as $banner) {
                $boxes['banner_'.$banner->getId()] = $this->renderView('CMBundle:Homepage:boxBanner.html.twig', array('banner' => $banner));
            }
*/

            /* Box fans */
            if ($this->get('security.context')->isGranted('ROLE_USER') && $request->get('_route') == 'homepage_fans') {
                $fansIds = $em->getRepository('CMBundle:Fan')->getFans($this->getUser()->getId(), true);
                if (!empty($fansIds)) {
                    $in = array('inUsers' => array(), 'inPages' => array(), 'inGroups' => array());
                    foreach ($fansIds as $fan) {
                        if (!is_null($fan->getUserId())) {
                            $in['inUsers'][] = $fan->getUserId();
                        } elseif (!is_null($fan->getPageId())) {
                            $in['inPages'][] = $fan->getPageId();
                        } elseif (!is_null($fan->getGroupId())) {
                            $in['inGroups'][] = $fan->getGroupId();
                        }
                    }
                    $fans = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('in' => $in, 'locale' => $request->getLocale())), $page, 30);
                    foreach ($fans as $post) {
                        $boxes['connection_'.$post->getId()] = $this->renderView('CMBundle:Homepage:boxPost.html.twig', array('post' => $post));
                    }
                }
            }

            /* Box connections */
            if ($this->get('security.context')->isGranted('ROLE_USER') && $request->get('_route') == 'homepage_connections') {
                $relationsIds = $em->getRepository('CMBundle:Relation')->getRelationsIdsPerUser($this->getUser()->getId());
                if (!empty($relationsIds)) {
                    $relationsIds = array_map(function($v) { return $v['fromUserId']; }, $relationsIds);
                    $connections = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('inUsers' => $relationsIds, 'locale' => $request->getLocale())), $page, 30);
                    foreach ($connections as $post) {
                        $boxes['connection_'.$post->getId()] = $this->renderView('CMBundle:Homepage:boxPost.html.twig', array('post' => $post));
                    }
                }
            }

            /* Posts */
/*
            $posts = $this->get('knp_paginator')->paginate($em->getRepository('CMBundle:Post')->getLastPosts(array('locale' => $request->getLocale())), $page, 15);
            foreach ($posts as $post) {
                $boxes['post_'.$post->getId()] = $this->renderView('CMBundle:Wall:post.html.twig', array('post' => $post));
            }
*/

            // $boxes['loadMore'] = $this->renderView('CMBundle:Homepage:loadMore.html.twig', array('paginationData' => $posts->getPaginationData()));

            return new JsonResponse($boxes);
        }

        return array();
    }

    /**
     * @Template
     */
    public function tabsAction($categoryPage = null)
    {
        return array(
            'categoryPage' => $categoryPage,
            'categories' => $this->getDoctrine()->getManager()->getRepository('CMBundle:HomepageCategory')->getCategories(false)
        );
        // $this->categorie  = HomepageCategoryQuery::getCategorie(false);
    }

    /**
     * @Template
     */
    public function boxAction(Request $request, $box)
    {
        $em = $this->getDoctrine()->getManager();

        if ($box->getType() == HomepageBox::TYPE_PARTNER) {
            switch ($box->getLeftSide())
            {
                case HomepageBox::SIDE_ARTICLES:
                    $articles = $em->getRepository('CMBundle:HomepageArchive')->getArticlesByPage($box->getPageId(), array('paginate' => false, 'limit' => 3));
                    break;
                case HomepageBox::SIDE_NEWS:
                    $news = $em->getRepository('CMBundle:Article')->getArticles(array('pageId' => $box->getPageId(), 'paginate' => false, 'limit' => 3));
                    break;
                case HomepageBox::SIDE_EVENTS:
                    $events = $em->getRepository('CMBundle:Event')->getEvents(array(
                        'page_id' => $box->getPageId(),
                        'paginate' => false,
                        'limit' => 5,
                        'locale'  => $request->getLocale(),
                    ));
                    // EventiQuery::create()->joinWithI18n(sfContext::getInstance()->getUser()->getCulture(), Criteria::INNER_JOIN)->filterByPageId($this->box->getPageId())->filterByDataInizio(array('min' => strtotime(date('Y-m-d') . ' 00:00:00')))->orderByDataInizio()->limit(5)->find();
                    break;
            }
        } elseif ($box->getType() == HomepageBox::TYPE_RUBRIC) {
            $articles = $em->getRepository('CMBundle:HomepageArchive')->getArticlesByCategory($box->getCategoryId(), array('paginate' => false, 'limit' => 3));
            // HomepageArchiveQuery::getArticlesPerCategory($this->box->getCategoryId(), null, 3);
        }

        return array(
            'box' => $box,
            'articles' => $articles,
            'news' => $news,
            'events' => $events
        );
    }

    /**
     * @Template
     */
    public function reviewsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('CMBundle:EntityCategoryTranslation')->findOneBy(array('slug' => 'article'));
        return array('objects' => $em->getRepository('CMBundle:HomepageArchive')->getArticlesByCategory($category->getTranslatable()->getId(), array('paginate' => false, 'limit' => 9)));
        // $this->oggetti = HomepageArchiveQuery::getArticlesPerCategory(1, false, 9);
    }

    /**
     * @Template
     */
    public function eventsAction(Request $request)
    {
        return array('events' => $this->getDoctrine()->getManager()->getRepository('CMBundle:Event')->getLastByVip(24, array('locale' => $request->getLocale())));
        // $this->photos = ImmaginiQuery::getLastByVip(1, 24);
    }

    /**
     * @Template
     */
    public function photoGalleryAction()
    {
        return array('photos' => $this->getDoctrine()->getManager()->getRepository('CMBundle:Image')->getLastByVip(1, 24));
        // $this->photos = ImmaginiQuery::getLastByVip(1, 24);
    }

    /**
     * @Template
     */
    public function videoGalleryAction()
    {
        return array('videos' => $this->getDoctrine()->getManager()->getRepository('CMBundle:Multimedia')->getLastByVip(24));
        // $this->videos = MultimediaQuery::getLastByVip(1, 24);
    }

    /**
     * @Template
     */
    public function lastPostsAction()
    {
        return array('posts' => $this->getDoctrine()->getManager()->getRepository('CMBundle:Post')->getLastPosts(array(
            'paginate' => false,
            'limit' => 10,
            'exclude' => array()
        )));
        // $this->posts = BachecaQuery::getLastPosts(null, 10, array('group_picture', 'group_biography', 'page_picture', 'page_biography', 'auditions', 'competitions'));
    }

    /**
     * @Template
     */
    public function recentAction(Request $request)
    {
        return array('articles' => $this->getDoctrine()->getManager()->getRepository('CMBundle:HomepageArchive')->getArticles(array(
            'paginate' => false,
            'limit' => 10,
            'locale' => $request->getLocale()
        )));
    }

    /**
     * @Template
     */
    public function writersAction()
    {
        $em = $this->getDoctrine()->getManager();

        return array(
            'writers' => $em->getRepository('CMBundle:HomepageArchive')->getWriters(),
            'nbArticles' => $em->getRepository('CMBundle:HomepageArchive')->getNbArticlesWriters()
        );
    }


    /**
     * @Template
     */
    public function partnerRadioClassicaAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();


        $page = $em->getRepository('CMBundle:Page')->findOneBy(array('slug' => 'sony-classical-italia'));

        return array(
            'articles' => $em->getRepository('CMBundle:HomepageArchive')->getArticlesByPage($page->getId(), array('paginate' => false, 'limit' => 3)),
            'events' => $em->getRepository('CMBundle:Event')->getEvents(array(
                'page_id' => $page->getId(),
                'paginate' => false,
                'limit' => 12,
                'locale' => $request->getLocale()
            ))
        );
        // $this->articoli = HomepageArchiveQuery::getArticlesPerPage(3, null, 3);
        // $this->eventi = EventiQuery::create()->joinWithI18n(sfContext::getInstance()->getUser()->getCulture(), Criteria::INNER_JOIN)->filterByPageId(3)->filterByDataInizio(array('min' => strtotime(date('Y-m-d') . ' 00:00:00')))->orderByDataInizio()->limit(12)->find();
    }

    /**
     * @Route("/category/{slug}/{page}", name = "homepage_category", requirements={"userId" = "\d+"})
     * @Template("CMBundle:Homepage:list.html.twig")
     */
    public function categoryAction(Request $request, $slug, $page = 1)
    {
        $em = $this->getDoctrine()->getManager();

        $category = $em->getRepository('CMBundle:HomepageCategory')->getCategory($slug, array(
            'locale' => $request->getLocale(),
        ));

        if (!$category) {
            throw new NotFoundHttpException($this->get('translator')->trans('Category not found.', array(), 'http-errors'));
        }


        // $this->getResponse()->addStylesheet('homepage', 'last');
        // $this->getResponse()->addJavascript('homepage', 'last');
        // $this->getResponse()->setTitle($this->categoria->getName().' - Il Portale della Musica Classica');
        // $this->setLayout('layoutNoColumn');

        // $objects = $em->getRepository('CMBundle:HomepageArchive')->getArticlesByCategory($box->getCategoryId(), array('paginate' => false, 'limit' => 3));
        // $this->oggetti = HomepageArchiveQuery::getArticlesPerCategory($this->categoria->getId(), $request->getParameter('page', 1));

        $articles = $em->getRepository('CMBundle:HomepageArchive')->getArticlesByCategory($category->getId(), array(
            'locale' => $request->getLocale(),
        ));

        $pagination = $this->get('knp_paginator')->paginate($articles, $page, 10);

        if ($request->isXmlHttpRequest())
        {
            return $this->renderPartial('oggetti', array('oggetti' => $this->oggetti, 'articoli' => $this->articoli));
        }

        return array('category' => $category, 'objects' => $pagination);
    }

    /**
     * @Route("/video/{id}", name = "homepage_video")
     * @Template
     */
    public function videoAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();
        // $this->setLayout('layoutNoColumn');
        $rows = $em->getRepository('CMBundle:HomepageRow')->getRows();
        $columns = $em->getRepository('CMBundle:HomepageColumn')->getColumns();
        return array('rows' => $rows, 'columns' => $columns);
    }

    /**
     * @Route("/photo/{id}", name = "homepage_photo")
     * @Template
     */
    public function photoAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();
        // $this->setLayout('layoutNoColumn');
        $rows = $em->getRepository('CMBundle:HomepageRow')->getRows();
        $columns = $em->getRepository('CMBundle:HomepageColumn')->getColumns();
        return array('rows' => $rows, 'columns' => $columns);
    }

    /**
     * @Route("/archive/{id}", name = "homepage_archive")
     * @Template
     */
    public function archiveAction($id = null)
    {
        $em = $this->getDoctrine()->getManager();
        // $this->setLayout('layoutNoColumn');
        $rows = $em->getRepository('CMBundle:HomepageRow')->getRows();
        $columns = $em->getRepository('CMBundle:HomepageColumn')->getColumns();
        return array('rows' => $rows, 'columns' => $columns);
    }

    /**
     * @Route("/{id}", name = "homepage_show", requirements={"id" = "\d+"})
     * @Template
     */
    public function showAction($id)
  {
    $this->getResponse()->addStylesheet('homepage', 'last');
    $this->getResponse()->addJavascript('homepage', 'last');

    $this->oggetto      = HomepageArchiveQuery::getArticle($request->getParameter('id'));

    $this->getResponse()->setTitle($this->oggetto->getStampa());
        $this->setLayout('layoutNoColumn');
  }
}