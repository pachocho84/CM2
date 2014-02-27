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
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function userAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        if ($user->getId() == $this->getUser()->getId()) {
            $relationTypes = $em->getRepository('CMBundle:Relation')->getRelationTypesPerUser($user->getId(), Relation::ACCEPTED_NO, true);
            $relations = $em->getRepository('CMBundle:Relation')->getRelationsPerUser($user->getId(), Relation::ACCEPTED_NO, true);
            $pendingRelations = $em->getRepository('CMBundle:Relation')->getRelationsPerUser($user->getId(), Relation::ACCEPTED_NO);
        } else {
            $relationTypes = $em->getRepository('CMBundle:Relation')->getRelationTypesPerUser($user->getId(), Relation::ACCEPTED_BOTH);
            $relations = $em->getRepository('CMBundle:Relation')->getRelationsPerUser($user->getId(), Relation::ACCEPTED_BOTH);
        }

        $groupedRelations = array();
        foreach ($relations as &$relation) {
            if (!array_key_exists($relation->getRelationTypeId(), $groupedRelations)) {
                $groupedRelations[$relation->getRelationTypeId()] = array();
            }
            $groupedRelations[$relation->getRelationTypeId()][] = $relation;
        }

        return array(
            'user' => $user,
            'pendingRelations' => $pendingRelations,
            'relationTypes' => $relationTypes,
            'relations' => $groupedRelations
        );
    }

    /**
     * @Route("relations/button/{userId}", name="relation_button", requirements={"userId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function buttonAction(Request $request, User $user = null, $userId = null, RelationType $relationTypePassed = null)
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

        $relationTypes = $em->getRepository('CMBundle:Relation')->getRelationTypesBetweenUsers($this->getUser()->getId(), $user->getId());

        $acceptedRelations = 0;
        $pendingRelations = 0;
        $reqText = 'Request a relation';
        $btnColour = 'danger';
        foreach ($relationTypes as $relType) {
            foreach ($relType->getRelations() as $relation) {
                // var_dump($relType->getName().' '.$relation->getId().' '.$relation->getAccepted().' '.$relation->getFromUserId().'->'.$relation->getUserId());
                if ($relation->getAccepted() == Relation::ACCEPTED_NO && $acceptedRelations == 0 && $pendingRelations == 0) {
                    $reqText = 'Respond to a relation request';
                    $btnColour = 'warning';
                } elseif ($relation->getAccepted() == Relation::ACCEPTED_UNI) {
                    if ($acceptedRelations == 0 && $pendingRelations == 0) {
                        $reqText = $relType->getName();
                        $btnColour = 'warning';
                    }
                    $pendingRelations++;
                } elseif ($relation->getAccepted() == Relation::ACCEPTED_BOTH) {
                    $acceptedRelations++;
                    $reqText = $relType->getName();
                    $btnColour = 'success';
                }
            }
        }
        // die;

        if ($acceptedRelations > 1) {
            $reqText = $acceptedRelations.' connections';
        } elseif ($acceptedRelations == 0 && $pendingRelations > 1) {
            $reqText = $pendingRelations.' connections';
        }
        if ($acceptedRelations != 0) {
            $btnColour = 'success';
        }

        if (!is_null($relationTypePassed)) {
            return new JsonResponse(array(
                'button' => $this->renderView('CMBundle:Relation:buttonItem.html.twig', array('reqText' => $reqText, 'btnColour' => $btnColour)),
                'item' => $this->renderView('CMBundle:Relation:item.html.twig', array('user' => $user, 'relationType' => $relationTypePassed))
            ));
        }

        return array(
            'user' => $user,
            'relationTypes' => $relationTypes,
            'reqText' => $reqText,
            'btnColour' => $btnColour
        );
    }

    /**
     * @Route("relations/add/{relationTypeId}/{userId}", name="relation_add", requirements={"relationTypeId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function addAction(Request $request, $relationTypeId, $userId)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('CMBundle:User')->findOneById($userId);
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found.', array(), 'http-errors'));
        }

        if ($user == $this->getUser()) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $relationType = $em->getRepository('CMBundle:RelationType')->findOneById($relationTypeId);

        if (!$relationType) {
            throw new NotFoundHttpException($this->get('translator')->trans('Relation type not found.', array(), 'http-errors'));
        }

        if ($em->getRepository('CMBundle:Relation')->countBy(array('userId' => $user->getId(), 'fromUserId' => $this->getUser()->getId(), 'relationTypeId' => $relationType->getId())) > 0) {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $relation = new Relation;
        $relation->setUser($user)
            ->setFromUser($this->getUser())
            ->setAccepted(Relation::ACCEPTED_UNI);

        $relationType->addRelation($relation);

        $em->persist($relation);

        $em->flush();

        if (!is_null($request->get('pending'))) {
            return $this->render('CMBundle:Relation:pending.html.twig', array('user' => $user, 'relation' => $relation));
        }
        return $this->forward('CMBundle:Relation:button', array('user' => $user, 'relationTypePassed' => $relationType));
    }

    /**
     * @Route("relations/update/{choice}/{id}", name="relation_update", requirements={"id" = "\d+", "choice"="accept|refuse"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function updateAction(Request $request, $id, $choice)
    {
        $em = $this->getDoctrine()->getManager();
     
        $relation = $em->getRepository('CMBundle:Relation')->findOneById($id);

        if (!$relation) {
            throw new NotFoundHttpException($this->get('translator')->trans('Relation not found.', array(), 'http-errors'));
        }

        if ($relation->getFromUserId() == $this->getUser()->getId()) {
            $user = $relation->getUser();
        } else {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        if ($choice == 'accept') {
            $relation->setAccepted(Relation::ACCEPTED_BOTH);
            $em->persist($relation);
        } elseif ($choice == 'refuse') {
            $em->remove($relation);
            $em->getRepository('CMBundle:Relation')->remove($relation->getRelationType()->getInverseTypeId(), $relation->getUserId(), $relation->getUserId());
        }

        $em->flush();

        return $this->forward('CMBundle:Relation:button', array('user' => $user, 'relationType' => $relation->getRelationType()));
        return $this->render('CMBundle:Relation:item.html.twig', array('user' => $user, 'relationType' => $relation->getRelationType()));
    }

    /**
     * @Route("relations/delete/{id}", name="relation_delete", requirements={"id" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
     
        $relation = $em->getRepository('CMBundle:Relation')->findOneById($id);

        if (!$relation) {
            throw new NotFoundHttpException($this->get('translator')->trans('Relation not found.', array(), 'http-errors'));
        }

        if ($relation->getFromUserId() == $this->getUser()->getId()) {
            $user = $relation->getUser();
        } else {
            throw new HttpException(403, $this->get('translator')->trans('You cannot do this.', array(), 'http-errors'));
        }

        $em->remove($relation);
        $em->getRepository('CMBundle:Relation')->remove($relation->getRelationType()->getInverseTypeId(), $relation->getUserId(), $relation->getUserId());

        $em->flush();

        if ($user->getId() == $this->getUser()->getId()) {
            $user = $request->getUser();
        }

        return $this->forward('CMBundle:Relation:button', array('user' => $user, 'relationType' => $relation->getRelationType()));
        return $this->render('CMBundle:Relation:item.html.twig', array('user' => $user, 'relationType' => $relation->getRelationType()));
    }
}