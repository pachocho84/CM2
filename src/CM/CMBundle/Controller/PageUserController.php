<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\EntityCategory;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\Disc;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\Article;
use CM\CMBundle\Entity\PageUser;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Sponsored;
use CM\CMBundle\Entity\Tag;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\DiscType;
use CM\CMBundle\Form\MultimediaType;
use CM\CMBundle\Form\ArticleType;
use CM\CMBundle\Form\ImageCollectionType;

class PageUserController extends Controller
{
    /**
     * @Route("/members/{id}", name="pageuser_members", requirements={"id" = "\d+"})
     * @Template
     */
    public function membersAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        return array(
            'members' => $em->getRepository('CMBundle:PageUser')->getActiveForEntity($id, array('locale' => $request->getLocale()))
        );
    }

    /**
     * @Route("/account/pages/{slug}/members", name="pageuser_members_settings", requirements={"pageNum" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")1
     * @Template
     */
    public function membersSettingsAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $page = $em->getRepository('CMBundle:Page')->getPage($slug, array('pageUsers' => true));
        
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        if (!$this->get('cm.user_authentication')->isAdminOf($page)) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $form = $this->createForm(new PageMembersType, $page, array(
            'cascade_validation' => true,
            'tags' => $em->getRepository('CMBundle:Tag')->getTags(array('type' => Tag::TYPE_USER, 'locale' => $request->getLocale())),
            'type' => 'CM\CMBundle\Form\PageUserType',
            'em' => $em
        ));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($page);
            $em->flush();

            return new RedirectResponse($this->generateUrl('pageuser_members_settings', array('slug' => $slug)));
        }

        return array(
            'page' => $page,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/members/promoteAdmin/{id}", name="pageuser_promote", requirements={"id" = "\d+"})
     * @Route("/members/removeAdmin/{id}", name="pageuser_remove", requirements={"id" = "\d+"})
     * @Template("CMBundle:PageUser:addPageUsers.html.twig")
     */
    public function promoteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneById($id);

        if (!$this->get('cm.user_authentication')->isAdminOf($pageUser->getPage())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $pageUser->setAdmin(($request->get('_route') == 'pageuser_promote'));
        $em->persist($pageUser);
        $em->flush();

        return array(
            'member' => $pageUser
        );
    }

    /**
     * @Route("/members/add/page/{pageId}", name="pageuser_add_pageusers", requirements={"pageId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addPageUsersAction(Request $request, $pageId)
    {
        $em = $this->getDoctrine()->getManager();

        $userId = $request->get('user_id');

        if (count($em->getRepository('CMBundle:PageUser')->findBy(array('pageId' => $pageId, 'userId' => $userId))) > 0) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $page = $em->getRepository('CMBundle:Page')->findOneById($pageId);
        if (!$page) {
            throw new NotFoundHttpException('Page not found.');
        }

        if ($userId != $this->getUser()->getId()) {
            if (!$this->get('cm.user_authentication')->isAdminOf($page)) {
                throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
            }

            $user = $em->getRepository('CMBundle:User')->findOneById($userId);
            if (!$user) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
        } else {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $page = new Page;

        $protagonistNewId = $request->query->get('protagonist_new_id');

        // add dummies
        foreach (range(0, $protagonistNewId - 1) as $i) {
            $page->addUser($this->getUser());
        }

        $page->addUser(
            $user,
            false, // admin
            PageUser::STATUS_PENDING
        );

        $form = $this->createForm(new PageMembersType, $page, array(
            'cascade_validation' => true,
            'tags' => $em->getRepository('CMBundle:Tag')->getTags(array('type' => Tag::TYPE_USER, 'locale' => $request->getLocale())),
            'type' => 'CM\CMBundle\Form\PageUserType',
            'em' => $em
        ));
        
        return array(
            'skip' => true,
            'newEntry' => true,
            'page' => $page,
            'pageUsers' => $form->createView()['pageUsers'],
            'protagonistNewId' => $protagonistNewId
        );
    }

    /**
     * @Route("/members/add/{pageId}", name="pageuser_add", requirements={"pageId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addAction(Request $request, $pageId)
    {
        $em = $this->getDoctrine()->getManager();

        if (count($em->getRepository('CMBundle:PageUser')->findBy(array('pageId' => $objectId, 'userId' => $this->getUser()->getId()))) > 0) {
             throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $page = $em->getRepository('CMBundle:Page')->findOneById($pageId);
        if (!$page) {
            throw new NotFoundHttpException($this->get('translator')->trans('Page not found.', array(), 'http-errors'));
        }

        $pageUser = new PageUser;
        $pageUser->setUser($this->getUser())
            ->setStatus(PageUser::STATUS_REQUESTED);
        $page->addPageUser($pageUser);
        $em->persist($page);
        $em->flush();

        return $this->render('CMBundle:PageUser:requestAdd.html.twig', array('page' => $page, 'pageUser' => $pageUser));
    }

    /**
     * @Route("/members/{choice}/{id}", name="pageuser_update", requirements={"id" = "\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function updateAction(Request $request, $id, $choice)
    {
        $em = $this->getDoctrine()->getManager();
     
        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneById($id);

        if (!$pageUser) {
            throw new NotFoundHttpException($this->get('translator')->trans('Protagonist not found.', array(), 'http-errors'));
        }

        if ($pageUser->getUserId() != $this->getUser()->getId() && !$this->get('cm.user_authentication')->isAdminOf($pageUser->getPage())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if ($choice == 'accept') {
            $pageUser->setStatus(PageUser::STATUS_ACTIVE);
        } elseif ($choice == 'refuse') {
            $pageUser->setStatus($pageUser->getStatus() == PageUser::STATUS_PENDING ? PageUser::STATUS_REFUSED_ENTITY_USER : PageUser::STATUS_REFUSED_ADMIN);
        }
        $em->persist($pageUser);
        $em->flush();

        return $this->render('CMBundle:PageUser:requestAdd.html.twig', array('page' => $pageUser->getPage(), 'pageUser' => $pageUser));
    }

    /**
     * @Route("/members/delete/{id}", name="pageuser_delete", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
     
        $pageUser = $em->getRepository('CMBundle:PageUser')->findOneById($id);

        if (!$pageUser) {
            throw new NotFoundHttpException($this->get('translator')->trans('Protagonist not found.', array(), 'http-errors'));
        }

        if ($pageUser->getUserId() != $this->getUser()->getId() && !$this->get('cm.user_authentication')->isAdminOf($pageUser->getPage())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->remove($pageUser);
        $em->flush();

        return $this->render('CMBundle:PageUser:requestAdd.html.twig', array('page' => $pageUser->getPage(), 'pageUser' => null));
    }
}
