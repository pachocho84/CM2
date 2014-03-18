<?php

namespace CM\CMBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\ORM\Translatable\CurrentLocaleCallable;
use CM\CMBundle\Entity\Work;
use CM\CMBundle\Form\WorkType;

class WorkController extends Controller
{
    /**
     * @Route("/account/work", name="user_work")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $works = $em->getRepository('CMBundle:Work')->findBy(
            array('userId' => $this->getUser()->getId()),
            array('dateFrom' => 'desc')
        );

        $work = new Work;
        $work->setUser($this->getUser());
        $form = $this->createForm(new WorkType(), $work, array(
            'cascade_validation' => true
        ))->add('save', 'submit');

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em->persist($work);
            $em->flush();

            return $this->render('CMBundle:Work:object.html.twig', array('work' => $work));
        }
        
        return array(
            'works' => $works,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{slug}/work", name="work_show")
     * @Template
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $works = $em->getRepository('CMBundle:Work')->findBy(
            array('userId' => $this->getUser()->getId()),
            array('dateFrom' => 'desc')
        );
        
        return array(
            'user' => $user,
            'works' => $works
        );
    }
}