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
use CM\CMBundle\Form\WorkCollectionType;

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
            array('dateFrom' => 'desc', 'id' => 'desc')
        );

        $work = new Work;
        $work->setUser($this->getUser());

        $works[] = $work;

        $form = $this->createForm(new WorkCollectionType, array('works' => new ArrayCollection($works)), array(
            'cascade_validation' => true,
            'expandable' => (is_null($work) ? '' : 'small')
        ))->add('save', 'submit');

        $form->handleRequest($request);
        
        if ($form->isValid()) {
            foreach ($works as $work) {
                $em->persist($work);
            }
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