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
use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as EWZ;
use Symfony\Component\Process\Exception\RuntimeException;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Fan;
use CM\CMBundle\Form\CommentType;

function getAllErrors($form, &$errors = array()) {
    foreach ($form->getErrors() as $error) {
        $errors[] = $form->getName().': '.$error->getMessage();
    }
    foreach ($form->all() as $child) {
        getAllErrors($child, $errors);
    }
    return $errors;
}

/**
 * @Route("/contacts")
 * @Template
 */
class ContactsController extends Controller
{
    /**
     * @Route("", name="contacts_index")
     * @Template
     */
    public function indexAction(Request $request)
    {
        $form = $this->createFormBuilder()->add('body', 'text', array(
                'label' => $this->get('translator')->trans('Say something'),
                'attr' => array('expandable' => ''),
                'constraints'   => array(
                    new Assert\NotBlank,
                    new Assert\Length(array('min' => 0, 'max' => 300))
                ),
                'error_bubbling' => false,
            ))->add('email', 'email', array(
                'constraints'   => array(
                    new Assert\NotBlank,
                    new Assert\Email
                ),
                'error_bubbling' => false,
            ))
            ->add('recaptcha', 'ewz_recaptcha', array(
                'constraints'   => array(
                    // new EWZ\True
                ),
                'error_bubbling' => false,
            ))->add('save', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                ->setSubject('Feedback')
                ->setFrom($form->getData()['email'])
                ->setTo('f.castellarin@gmail.com')
                ->setBody($form->getData()['body'])
            ;
            $this->get('mailer')->send($message);

            $this->get('session')->getFlashBag()->add(
                'email',
                $this->get('translator')->trans('Your message was sent, thank you for the feedback!')
            );

            return new RedirectResponse($this->generateUrl('contacts_index'));
        }

        return array('form' => $form->createView());
    }

    /**
     * @Route("/sent", name="contacts_sent")
     * @Template
     */
    public function sentAction()
    {
        return array();
    }
}