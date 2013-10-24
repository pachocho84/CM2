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
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Sponsored;
use CM\CMBundle\Form\EventType;
use CM\CMBundle\Form\ImageCollectionType;

/**
 * @Route("/protagonist")
 */
class EntityUserController extends Controller
{
    /**
     * @Route("/add", name="entityuser_add")
     * @Route("/addGroup", name="entityuser_add_group")
     * @Route("/addPage", name="entityuser_add_page")
     * @Template
     */
    public function addEntityUsersAction(Request $request)
    {
        // if (!$request->isXmlHttpRequest() || !$this->get('cm_user.authentication')->isAuthenticated()) {
        //     throw new HttpException(401, 'Unauthorized access.');
        // }
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
            // throw exception
        }

        $event = new Event;
            
        $protagonist_new_id = $request->query->get('protagonist_new_id');

        // add dummies
        foreach (range(0, $protagonist_new_id - 1) as $i) {
            $event->addUser($this->getUser());
        }

        foreach ($users as $user) {    
            $event->addUser(
                $user,
                false, // admin
                EntityUser::STATUS_ACTIVE,
                true // notifications
            );
        }

        $form = $this->createForm(new EventType, $event, array(
            'cascade_validation' => true,
            'error_bubbling' => false,
            'em' => $em,
            'is_admin' => $this->get('security.context')->isGranted('ROLE_ADMIN'),
            'user_tags' => $em->getRepository('CMBundle:UserTag')->getUserTags(array('locale' => $request->getLocale())),
            'locales' => array('en'/* , 'fr', 'it' */),
            'locale' => $request->getLocale()
        ));
        
        return array(
            'skip' => true,
            'newEntry' => true,
            'entity' => $event,
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
        var_dump($user_ids); die;
        } elseif (!is_null($request->query->get('page_id'))) {
            $page_ids = explode(',', $request->query->get('page_id'));
            $user_ids = $em->getRepository('CMBundle:Page')->getUserIdsFor($page_ids);
        } else {
            // throw exception
        }

        return new JsonResponse($user_ids);
    }
}
