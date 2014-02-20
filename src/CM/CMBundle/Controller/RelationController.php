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
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Relation;

class RelationController extends Controller
{
    /**
     * @Route("/{slug}/relations", name="relation_user")
     * @Template
     */
    public function userAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $relations = $em->getRepository('CMBundle:Relation')->getUserRelations($user->getId());

        $inverses = array();
        foreach ($relations as $key => $relation) {
            if ($relation->getUserId() == $user->getId()) {
                $inverses[$relation->getFromUserId()] = $relation;
                unset($relations[$key]);
            }
        }
        
        return array(
            'user' => $user,
            'relations' => $relations,
            'inverses' => $inverses
        );
    }

    /**
     * @Route("relations/button/{userId}", name="relation_button", requirements={"userId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function buttonAction(User $user = null, $userId = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (is_null($user)) {
            $user = $em->getRepository('CMBundle:User')->findOneById($userId);
            
            if (!$user) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
        }

        if ($user->getId() == $this->getUser()->getId()) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this 1.', array(), 'http-errors'));
        }

        $relationTypes = $em->getRepository('CMBundle:RelationType')->getTypesPerUser($userId);

        $requests = $em->getRepository('CMBundle:Request')->getRequestsFor($user->getId(), $this->getUser()->getId(), array('object' => Relation::className(), 'indexBy' => 'objectId'));

        if (count($requests) > 0) {
            $relations = $em->getRepository('CMBundle:Relation')->findBy(array('userId' => $user->getId(), 'fromUserId' => $this->getUser()->getId()));

            $keys = array();
            foreach ($relations as $relation) {
                $keys[] = $relation->getRelationTypeId();
            }
            $relations = array_combine($keys, $relations);
        }

        // if (is_null($relation)) {
        //     $relation = $em->getRepository('CMBundle:Relation')->findOneBy(array('userId' => $user->getId(), 'fromUserId' => $this->getUser()->getId()));
        // }
        // if (!is_null($relation)) {
        //     $inverse = $em->getRepository('CMBundle:Relation')->getInverse($relation->getType(), $relation->getUserId(), $relation->getFromUserId());
        // } else {
        //     $inverse = null;
        // }

        // $request = $em->getRepository('CMBundle:Request')->findOneBy(array('fromUserId' => $user->getId(), 'userId' => $this->getUser()->getId(), 'object' => get_class($relation)));
        // if (is_null($request)) {
        //     $request = $em->getRepository('CMBundle:Request')->findOneBy(array('userId' => $user->getId(), 'fromUserId' => $this->getUser()->getId(), 'object' => get_class($relation)));
        // }

        return array('user' => $user, 'relationTypes' => $relationTypes, 'requests' => $requests, 'relations' => $relations);
    }
}