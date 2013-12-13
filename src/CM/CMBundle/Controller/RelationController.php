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
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function buttonAction(User $user = null, $userId = null, $relation = null)
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

        if (is_null($relation)) {
            $relation = $em->getRepository('CMBundle:Relation')->findOneBy(array('userId' => $user->getId(), 'fromUserId' => $this->getUser()->getId()));
        }
        if (!is_null($relation)) {
            $inverse = $em->getRepository('CMBundle:Relation')->getInverse($relation->getType(), $relation->getUserId(), $relation->getFromUserId());
        } else {
            $inverse = null;
        }

        return array('user' => $user, 'relation' => $relation, 'inverse' => $inverse);
    }

    /**
     * @Route("/relation/add/{slug}", name="relation_add")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:Relation:button.html.twig")
     */
    public function addAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found. 1', array(), 'http-errors'));
        }

        $relation = new Relation;
        $relation->setFromUser($user)
            ->setUser($this->getUser())
            ->setAccepted(false)
            ->setType(Relation::inverseType($request->get('type')));
        $em->persist($relation);

        $em->flush();
        
        return $this->buttonAction($user);
    }
    
    /**
     * @Route("/relation/accept/{slug}/{id}", name="relation_accept")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:Relation:button.html.twig")
     */
    public function acceptAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException($this->get('translator')->trans('User not found. 1', array(), 'http-errors'));
        }

        $relation = $em->getRepository('CMBundle:Relation')->findOneBy(array('id' => $id,'userId' => $user->getId(), 'fromUserId' => $this->getUser()->getId()));

        if (!$relation) {
            throw new NotFoundHttpException($this->get('translator')->trans('Relation not found.', array(), 'http-errors'));
        }

        $relation->setAccepted(true);
        $em->persist($relation);

        $em->flush();
        
        return $this->buttonAction($user, null, $relation);
    }
    
    /**
     * @Route("/relation/delete/{slug}/{id}", name="relation_delete")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template("CMBundle:Relation:button.html.twig")
     */
    public function deleteAction(Request $request, $type, $id)
    {
        if (!$this->get('security.context')->isGranted('ROLE_USER')) {
              throw new HttpException(401, 'Unauthorized access.'); 
        }
        
        $em = $this->getDoctrine()->getManager();
        $like = $em->getRepository('CMBundle:Like')->findOneBy(array(
            $type => $id,
            'user' => $this->getUser()
        ));
        $em->remove($like);
        $em->flush();

        if ($request->isXmlHttpRequest()) { 
            if ($type == 'post') {
                $post = $em->getRepository('CMBundle:Post')->find($id);
            } elseif ($type == 'image') {
                $post = $em->getRepository('CMBundle:Image')->find($id);
            }
            
            if (is_null($post)) {
                throw $this->createNotFoundException('Bad request.');
            }

            return new JsonResponse(array(
                'likes' => $this->renderView('CMBundle:Like:like.html.twig', array('post' => $post)), 
                'likeActions' => $this->renderView('CMBundle:Like:likeActions.html.twig', array('post' => $post)),
                'likeCount' => $this->renderView('CMBundle:Like:likeCount.html.twig', array('post' => $post)),
            ));
        }

        $this->get('session')->getFlashBag('confirm', 'You don\'t like this anymore.');
        return new RedirectResponse($request->get('referer'));
    }
}