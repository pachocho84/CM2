<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Fan;
use CM\CMBundle\Form\CommentType;

class FanController extends Controller
{
    /**
     * @Route("/{slug}/fans", name="fan_user")
     * @Template
     */
    public function userAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $fans = $em->getRepository('CMBundle:Fan')->getUserFans($user->getId());

        $imFanOf = null;
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $imFanOf = $em->getRepository('CMBundle:Fan')->getFanOf($this->getUser()->getId());

            $imFanOf = empty($imFanOf) ? false : in_array($this->getUser(), $imFanOf);
        }
        
        // $this->getResponse()->setTitle($this->getContext()->getI18N()->__($this->user->getId() == $this->getUser()->getId() ? 'Your fans' : '%user%\'s fans', array('%user%' => $this->user)));
        
        return array(
            'user' => $user,
            'fans' => $fans,
            'imFanOf' => $imFanOf
        );
    }
    
    public function whoIsFanAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // $this->oggetti = FansQuery::findFans(null, true);
        // $this->whoImFanOf = FansQuery::whoImFanOf();
        
        // $this->getResponse()->setTitle($this->getContext()->getI18N()->__($this->getContext()->getSito()->getFanLabel()));

        $fans = $em->getRepository('CMBundle:Fan')->getUserFans($user->getId());
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $whoImFanOf = $em->getRepository('CMBundle:Fan')->getFansOf($this->getUser()->getId());
        }

        return array(
            'objects' => $em->getRepository('CMBundle:Fan')->getUserFans($user->getId()),
            'whoImFanOf' => $em->getRepository('CMBundle:Fan')->getWhoImFanOf()
        );
    }
    
    /**
     * @Route("/fan/become/{object}/{fanId}", name="fan_become", requirements={"fanId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function becomeFanAction(Request $request, $object, $fanId)
    {
        $fanId = intval($fanId);

        if ($fanId == $this->getUser()->getId()) {
            throw new HttpException(403, 'You cannot become fan of yourself.');
        }

        $em = $this->getDoctrine()->getManager();

        $imFan = $em->getRepository('CMBundle:Fan')->checkIfIsFanOf($this->getUser(), $fanId, $object);
        
        if ($imFan == false) {      
            $fan = new Fan;
            $this->getUser()->addFanOf($fan);
            if ($object == 'User') {
                $em->getRepository('CMBundle:User')->findOneById($fanId)->addFan($fan);
            } elseif ($object == 'Page') {
                $em->getRepository('CMBundle:Page')->findOneById($fanId)->addFan($fan);
            } elseif ($object == 'Group') {
                $em->getRepository('CMBundle:Group')->findOneById($fanId)->addFan($fan);
            }
            $em->persist($fan);
            $em->flush();
        }

        // if ($request->isXmlHttpRequest()) {
        return new Response($this->renderView('CMBundle:Fan:fanButton.html.twig', array(
            'userId' => $fanId,
            'object' => $object,
            'imFan' => true,
        )));
        // }

        return new Response($this->renderView('CMBundle:Fan:fanButton.html.twig', array('userId' => $fanId, 'imFan' => $imFan, 'class' => $request->get('class'), 'fanBecomeText' => $request->get('fanBecomeText'))));
        
        // $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('You became fan.'));
        // $this->redirect($request->getReferer() ? $request->getReferer() : '@homepage');
    }
    
    /**
     * @Route("/fan/unbecome/{object}/{fanId}", name="fan_unbecome", requirements={"fanId" = "\d+"})
     * @JMS\Secure(roles="ROLE_USER")
     */
    public function unbecomeFanAction(Request $request, $object, $fanId)
    {
        $fanId = intval($fanId);

        if ($fanId == $this->getUser()->getId()) {
            throw new HttpException(403, 'You cannot become fan of yourself.');
        }

        $em = $this->getDoctrine()->getManager();

        if ($object == 'User') {
            $fan = $em->getRepository('CMBundle:Fan')->findOneBy(array('userId' => $fanId, 'fromUserId' => $this->getUser()->getId()));
        } elseif ($object == 'Page') {
            $fan = $em->getRepository('CMBundle:Fan')->findOneBy(array('pageId' => $fanId, 'fromUserId' => $this->getUser()->getId()));
        } elseif ($object == 'Group') {
            $fan = $em->getRepository('CMBundle:Fan')->findOneBy(array('groupId' => $fanId, 'fromUserId' => $this->getUser()->getId()));
        }

        $em->remove($fan);
        $em->flush();
        
        // if ($request->isXmlHttpRequest()) {
        return new Response($this->renderView('CMBundle:Fan:fanButton.html.twig', array(
            'userId' => $fanId,
            'object' => $object,
            'imFan' => false,
        )));
        // }
        
        return new Response($this->renderView('CMBundle:Fan:fanButton.html.twig', array('userId' => $fanId, 'imFan' => false)));


        // $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('You are not fan anymore.'));
        // $this->redirect($request->getReferer() ? $request->getReferer() : '@homepage');     
    }

    
    /**
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function fanButtonAction(Request $request, $userId, $object = 'User')
    {
        $em = $this->getDoctrine()->getManager();

        $imFan = $em->getRepository('CMBundle:Fan')->checkIfIsFanOf($this->getUser(), $userId, $object);

        return array(
            'userId' => $userId,
            'object' => $object,
            'imFan' => $imFan,
        );
    }
    
    public function executeUserFans(Request $request)
    {
        $this->fans = FanQuery::getUserFans($this->user->getId(), $this->limit ? $this->limit : 28);
        $this->nbFans = FanQuery::countUserFans($this->user->getId());
    }
}