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
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Form\CommentType;


     // * @JMS\Secure(roles="ROLE_USER") 

/**
 * @Route("/fan")
 */
class FanController extends Controller
{
    /**
     * @Route("/user/{slug}", name="fan_user")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function executeUser(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));
        
        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }
        
        // $this->getResponse()->setTitle($this->getContext()->getI18N()->__($this->user->getId() == $this->getUser()->getId() ? 'Your fans' : '%user%\'s fans', array('%user%' => $this->user)));
        
        return array(
            'fans' => $em->getRepository('CMBundle:Fan')->getUserFans($this->user->getId()),
            'whoImFanOf' => $em->getRepository('CMBundle:Fan')->getWhoImFanOf()
        );
    }
    
    public function executeWhoIsFan(Request $request)
    {         
        // $this->oggetti = FansQuery::findFans(null, true);
        // $this->whoImFanOf = FansQuery::whoImFanOf();
        
        // $this->getResponse()->setTitle($this->getContext()->getI18N()->__($this->getContext()->getSito()->getFanLabel()));
        return array(
            'objects' => $em->getRepository('CMBundle:Fan')->findFans(),
            'whoImFanOf' => $em->getRepository('CMBundle:Fan')->getWhoImFanOf()
        );
    }
    
    public function executeBecomeFan(Request $request, $fanId, $object)
    { 
        $imFan = $em->getRepository('CMBundle:Fan')->checkIfImFan($this->getUser(), $fanId, $object);
        
        if ($imFan == false) {      
            $fan = new Fan();
            if ($object == 'User') {
                $fan->setUserId($fanId);
            } elseif ($object == 'Page') {
                $fan->setPageId($fanId);
            } elseif ($object == 'Group') {
                $fan->setGroupId($fanId);
            }
            $fan->setFromUserId($this->getUser()->getId());
            $imFan = $fan->save();
        }
        
        if ($request->isXmlHttpRequest()) {
            return $this->renderView('CMBundle:Fan:fanButton', array('userId' => $fanId, 'imFan' => $imFan));
        }
        
        // $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('You became fan.'));
        // $this->redirect($request->getReferer() ? $request->getReferer() : '@homepage');
    }
    
    public function executeUnbecomeFan(Request $request)
    {
        if ($request->getParameter('object') == 'user') {
            $fan = FanQuery::create()->findByArray(array('UserId' => $request->getParameter('fan_id'), 'FromUserId' => $this->getUser()->getId()));
        } elseif ($request->getParameter('object') == 'page') {
            $fan = FanQuery::create()->findByArray(array('PageId' => $request->getParameter('fan_id'), 'FromUserId' => $this->getUser()->getId()));
        } elseif ($request->getParameter('object') == 'group') {
            $fan = FanQuery::create()->findByArray(array('GroupId' => $request->getParameter('fan_id'), 'FromUserId' => $this->getUser()->getId()));
        }
        $fan->delete();
        
        if ($request->isXmlHttpRequest()) {
            return $this->renderPartial('fanButton', array('user_id' => $request->getParameter('fan_id'), 'imFan' => false));
        }   
        
        $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__('You are not fan anymore.'));
        $this->redirect($request->getReferer() ? $request->getReferer() : '@homepage');     
    }

    public function executeFanButton(Request $request)
    {
        $this->imFan = FanQuery::checkIfImFan($this->user_id);
    }
    
    public function executeUserFans(Request $request)
    {
        $this->fans = FanQuery::getUserFans($this->user->getId(), $this->limit ? $this->limit : 28);
        $this->nbFans = FanQuery::countUserFans($this->user->getId());
    }
}