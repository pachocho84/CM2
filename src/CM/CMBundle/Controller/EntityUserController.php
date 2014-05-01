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
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Sponsored;
use CM\CMBundle\Entity\Tag;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\DiscType;
use CM\CMBundle\Form\MultimediaType;
use CM\CMBundle\Form\ArticleType;
use CM\CMBundle\Form\ImageCollectionType;

/**
 * @Route("/protagonists")
 */
class EntityUserController extends Controller
{
    /**
     * @Route("/{id}", name="entityuser_protagonists", requirements={"id" = "\d+"})
     * @Template
     */
    public function protagonistsAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        return array(
            'protagonists' => $em->getRepository('CMBundle:EntityUser')->getActiveForEntity($id, array('locale' => $request->getLocale()))
        );
    }

    /**
     * @Route("/{type}/{id}", name="entityuser_publisher", requirements={"type" = "user|page", "id" = "\d+"})
     * @Template
     */
    public function publisherAction(Request $request, $type, $id)
    {
        $em = $this->getDoctrine()->getManager();

        switch ($type) {
            case 'user':
                $publisher = $em->getRepository('CMBundle:User')->getWithTags($id, array('locale' => $request->getLocale()));
                break;
            case 'page':
                $publisher = $em->getRepository('CMBundle:'.ucfirst($type))->findOneById($id);
                break;
        }

        $func = 'get'.ucfirst($type).'Biography';
            
        return array(
            'type' => $type,
            'publisher' => $publisher,
            'biography' => $em->getRepository('CMBundle:Biography')->$func($publisher->getId(), array('locale' => $request->getLocale()))
        );
    }
    
    /**
     * @Route("/add/{object}", name="entityuser_add_entityusers", requirements={"type"="article|disc|event|multimedia"})
     * @Route("/addPage/{object}", name="entityuser_add_page")
     * @Template
     */
    public function addEntityUsersAction(Request $request, $object)
    {
        if (!$request->isXmlHttpRequest() || !$this->get('cm.user_authentication')->isAuthenticated()) {
            throw new HttpException(401, 'Unauthorized access.');
        }
        
        $em = $this->getDoctrine()->getManager();

        if (!is_null($request->query->get('user_id'))) {
            $userId = intval($request->query->get('user_id'));

            $users = array($em->getRepository('CMBundle:User')->findOneById($userId));
        } elseif (!is_null($request->query->get('page_id'))) {
            $pageId = $request->query->get('page_id');

            $excludes = explode(',', $request->query->get('exclude'));
            $users = $em->getRepository('CMBundle:Page')->getUsersFor($pageId, $excludes);

            $target = array('page_id', $pageId);
        } else {
            throw new HttpException(401, 'Unauthorized access.');
        }

        switch ($object) {
            case 'event':
                $entity = new Event;
                $formType = new EventType;
                break;
            case 'disc':
                $entity = new Disc;
                $formType = new DiscType;
                break;
            case 'multimedia':
                $entity = new Multimedia;
                $formType = new MultimediaType;
                break;
            case 'article':
                $entity = new Article;
                $formType = new ArticleType;
                break;
        }

        $protagonistNewId = $request->query->get('protagonist_new_id');

        // add dummies
        foreach (range(0, $protagonistNewId - 1) as $i) {
            $entity->addUser($this->getUser());
        }

        foreach ($users as $user) {    
            $entity->addUser(
                $user,
                false, // admin
                EntityUser::STATUS_PENDING,
                true // notifications
            );
        }

        $form = $this->createForm($formType, $entity, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'roles' => $user->getRoles(),
            'tags' => $em->getRepository('CMBundle:Tag')->getTags(array('type' => Tag::TYPE_ENTITY_USER, 'locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ));
        
        return array(
            'skip' => true,
            'newEntry' => true,
            'entity' => $entity,
            'entityUsers' => $form->createView()['entityUsers'],
            'target' => $target,
            'joinEntityType' => 'join'.$this->get('cm.helper')->className($entity->className()), // TODO: caluculate it
            'protagonistNewId' => $protagonistNewId
        );
    }

    /**
     * @Route("/removePage", name="entityuser_remove_page")
     */
    public function removePageAction(Request $request)
    {
        $pageIds = explode(',', $request->query->get('page_id'));
        $userIds = $em->getRepository('CMBundle:Page')->getUserIdsFor($pageIds);

        return new JsonResponse($user_ids);
    }

    /**
     * @Route("/add/{entityType}/{entityId}", name="entityuser_add", requirements={"entityType"="article|disc|event", "entityId"="\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addAction(Request $request, $entityType, $entityId)
    {
        $em = $this->getDoctrine()->getManager();

        if (count($em->getRepository('CMBundle:EntityUser')->findBy(array('entityId' => $objectId, 'userId' => $this->getUser()->getId()))) > 0) {
             throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $entity = $em->getRepository('CMBundle:'.ucfirst($entityType))->findOneById($entityId);
        if (!$entity) {
            throw new NotFoundHttpException($this->get('translator')->trans(ucfirst($entityType).' not found.', array(), 'http-errors'));
        }

        $entityUser = new EntityUser;
        $entityUser->setUser($this->getUser())
            ->setStatus(EntityUser::STATUS_REQUESTED);
        $entity->addEntityUser($entityUser);
        $em->persist($entity);
        $em->flush();

        return $this->render('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $entity, 'entityUser' => $entityUser));
    }

    /**
     * @Route("/{choice}/{id}", name="entityuser_update", requirements={"id" = "\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function updateAction(Request $request, $id, $choice)
    {
        $em = $this->getDoctrine()->getManager();
     
        $entityUser = $em->getRepository('CMBundle:EntityUser')->findOneById($id);

        if (!$entityUser) {
            throw new NotFoundHttpException($this->get('translator')->trans('Protagonist not found.', array(), 'http-errors'));
        }

        if ($entityUser->getUserId() != $this->getUser()->getId() && !$this->get('cm.user_authentication')->canManage($entityUser->getEntity())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if ($choice == 'accept') {
            $entityUser->setStatus(EntityUser::STATUS_ACTIVE);
        } elseif ($choice == 'refuse') {
            $entityUser->setStatus($entityUser->getStatus() == EntityUser::STATUS_PENDING ? EntityUser::STATUS_REFUSED_ENTITY_USER : EntityUser::STATUS_REFUSED_ADMIN);
        }
        $em->persist($entityUser);
        $em->flush();
        die;

        return $this->render('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $entityUser->getEntity(), 'entityUser' => $entityUser));
    }

    /**
     * @Route("/delete/{id}", name="entityuser_delete", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
     
        $entityUser = $em->getRepository('CMBundle:EntityUser')->findOneById($id);

        if (!$entityUser) {
            throw new NotFoundHttpException($this->get('translator')->trans('Protagonist not found.', array(), 'http-errors'));
        }

        if ($entityUser->getUserId() != $this->getUser()->getId() && !$this->get('cm.user_authentication')->canManage($entityUser->getEntity())) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->remove($entityUser);
        $em->flush();

        return $this->render('CMBundle:EntityUser:requestAdd.html.twig', array('entity' => $entityUser->getEntity(), 'entityUser' => null));
    }
}
