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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
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
    		'protagonists' => $em->getRepository('CMBundle:EntityUser')->getActiveForEntity($id),
            'tags' => $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale()))
        );
    }
    
    /**
     * @Route("/add/{object}", name="entityuser_add")
     * @Route("/addGroup/{object}", name="entityuser_add_group")
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
            $user_id = intval($request->query->get('user_id'));

            $users = array($em->getRepository('CMBundle:User')->findOneById($user_id));
        } elseif (!is_null($request->query->get('group_id'))) {
            $group_id = $request->query->get('group_id');

            $excludes = explode(',', $request->query->get('exclude'));
            $group = $em->getRepository('CMBundle:Group')->getGroupExcludeUsers($group_id, $excludes);

            $users = array();
            foreach ($group->getGroupUsers() as $groupUser) {
                $users[] = $groupUser->getUser();
            }

            $target = array('type' => 'group', 'obj' => $group);
        } elseif (!is_null($request->query->get('page_id'))) {
            $page_id = $request->query->get('page_id');

            $excludes = explode(',', $request->query->get('exclude'));
            $user_ids = $em->getRepository('CMBundle:Page')->getUserIdsFor($page_id, $excludes);

            $target = array('page_id', $page_id);
        } else {
            throw new HttpException(401, 'Unauthorized access.');
        }

        switch ($object) {
            case 'Event':
                $entity = new Event;
                $formType = new EventType;
                break;
            case 'Disc':
                $entity = new Disc;
                $formType = new DiscType;
                break;
            case 'Multimedia':
                $entity = new Multimedia;
                $formType = new MultimediaType;
                break;
            case 'Article':
                $entity = new Article;
                $formType = new ArticleType;
                break;
        }
        

        $protagonist_new_id = $request->query->get('protagonist_new_id');

        // add dummies
        foreach (range(0, $protagonist_new_id - 1) as $i) {
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
            'user_tags' => $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ));
        
        return array(
            'skip' => true,
            'newEntry' => true,
            'entity' => $entity,
            'entityUsers' => $form->createView()['entityUsers'],
            'target' => $target,
            'joinEntityType' => 'joinEvent', // TODO: caluculate it
            'protagonist_new_id' => $protagonist_new_id
        );
    }

    /**
     * @Route("/removeGroup", name="entityuser_remove_group")
     * @Route("/removePage", name="entityuser_remove_page")
     */
    public function removeProtagonistAction(Request $request)
    {
        if (!is_null($request->query->get('group_id'))) {
            $group_ids = explode(',', $request->query->get('group_id'));
            $user_ids = $em->getRepository('CMBundle:Group')->getUserIdsFor($group_ids);
        } elseif (!is_null($request->query->get('page_id'))) {
            $page_ids = explode(',', $request->query->get('page_id'));
            $user_ids = $em->getRepository('CMBundle:Page')->getUserIdsFor($page_ids);
        } else {
            // throw exception
        }

        return new JsonResponse($user_ids);
    }
}
