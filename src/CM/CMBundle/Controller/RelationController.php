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
use CM\CMBundle\Entity\RelationType;
use CM\CMBundle\Form\RelationTypeType;

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
    public function buttonAction(Request $request, User $user = null, $userId = null)
    {
        $em = $this->getDoctrine()->getManager();
        
        if (is_null($user)) {
            $user = $em->getRepository('CMBundle:User')->findOneById($userId);
            
            if (!$user) {
                throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
            }
        }

        if ($user->getId() == $this->getUser()->getId()) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $relationTypes = $em->getRepository('CMBundle:RelationType')->getTypesPerUser($this->getUser()->getId());

        $requests = $em->getRepository('CMBundle:Request')->getRequestsFor($user->getId(), $this->getUser()->getId(), array('object' => Relation::className(), 'indexBy' => 'objectId'));

        if (count($requests) > 0) {
            $relations = $em->getRepository('CMBundle:Relation')->getRelations($user->getId(), $this->getUser()->getId(), array('indexBy' => 'relationTypeId'));
        }

        // var_dump(array_keys($requests), $relations);die;

        $form = $this->createForm(new RelationTypeType, null, array(
            'action' => $this->generateUrl('relation_add_private_network')
        ))->add('create', 'submit', array('attr' => array('class' => 'col-sm-3')))->createView();

        return array('user' => $user, 'relationTypes' => $relationTypes, 'requests' => $requests, 'relations' => $relations, 'form' => $form);
    }

    /**
     * @Route("relations/addPrivateNetwork", name="relation_add_private_network")
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addPrivateNetworkAction(Request $request)
    {
        $relationType = new RelationType;
        $relationType->setUser($this->getUser());

        $form = $this->createForm(new RelationTypeType, $relationType);
        
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if (!is_null($em->getRepository('CMBundle:RelationType')->findOneBy(array('userId' => $this->getUser()->getId(), 'name' => $request->get('cm_cmbundle_relationtype')['name'])))) {
                throw new HttpException(403, $this->get('translator')->trans('Relation type already added.', array(), 'http-errors'));
            }
            
            $em->persist($relationType);
            $em->flush();
        }

        return new Response;
    }

    /**
     * @Route("relations/addToPrivate", name="relation_add_to_private")
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addToPrivateAction(Request $request)
    {
        $user = $em->getRepository('CMBundle:User')->findOneById($userId);
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }

        if ($user == $this->getUser()) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $relationType = $em->getRepository('CMBundle:RelationType')->findOneById($request->get('type'));

        $relation = new Relation;
        $relation->setFromUser($this->getUser())
            ->setUser($user)
            ->setAccepted(false)
            ->setRelationType($relationType);
        $em->persist($relation);

        $em->flush();

        return $this->forward('CMBundle:Relation:button', array('user' => $user));
    }
}