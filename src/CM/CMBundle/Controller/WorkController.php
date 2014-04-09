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

        $blankWork = new Work;
        $blankWork->setUser($this->getUser());

        $works[] = $blankWork;

        $form = $this->createForm(new WorkCollectionType, array('works' => $works), array(
            'cascade_validation' => true
        ))->add('save', 'submit');

        $form->handleRequest($request);

        $isValid = $form->isValid();
        if ($isValid) {
            foreach ($form->getData()['works'] as $work) {
                if (count($this->get('validator')->validate($work)) > 0) {
                    $works = $form->getData()['works'];
                    $isValid = false;
                    break;
                }
            }
        }

        if ($isValid) {
            $newWorks = $form->getData()['works'];
            $toBeRemoved = array();
            foreach ($works as $key => $work) {
                if (!in_array($work, $form->getData()['works'])) {
                    $toBeRemoved[] = $work;
                    unset($works[$key]);
                }
            }
            
            foreach ($toBeRemoved as $work) {
                $em->remove($work);
            }
            foreach ($newWorks as $work) {
                $work->setUser($this->getUser());
                $em->persist($work);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('user_work'));
        } else {
            $works = $form->getData()['works'];
            $works[] = $blankWork;
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