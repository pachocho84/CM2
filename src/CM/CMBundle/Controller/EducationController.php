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
use CM\CMBundle\Entity\Education;
use CM\CMBundle\Form\EducationType;

class EducationController extends Controller
{
    /**
     * @Route("/account/education", name="user_education")
     * @JMS\Secure(roles="ROLE_USER")
     * @Template
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $educations = $em->getRepository('CMBundle:Education')->findBy(array('userId' => $this->getUser()->getId()));

        $education = new Education;
        $education->setUser($this->getUser());
        $form = $this->createForm(new EducationType(), $education, array(
            'cascade_validation' => true
        ))->add('save', 'submit');

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em->persist($education);
            $em->flush();

            // return $this->render('CMBundle:Education:object.html.twig', array('education', $education));
        }
        
        return array(
            'educations' => $educations,
            'form' => $form->createView()
        );
    }
    
    /**
     * @Route("/{slug}/education", name="education_show")
     * @Template
     */
    public function showAction(Request $request, $slug)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('CMBundle:User')->findOneBy(array('usernameCanonical' => $slug));

        if (!$user) {
            throw new NotFoundHttpException('User not found.');
        }

        $educations = $em->getRepository('CMBundle:Education')->findBy(array('userId' => $user->getId()));
        
        return array(
        	'user' => $user,
            'educations' => $educations
        );
    }
}