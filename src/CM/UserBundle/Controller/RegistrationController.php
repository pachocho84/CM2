<?php

namespace CM\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\RegistrationController as BaseSecurityController;

class RegistrationController extends BaseSecurityController
{
    public function registerAction(Request $request, $template = 'CMUserBundle:Registration:registerLayout.html.twig', $templateArgs = array())
    {
        if (!is_null($template)) {
            return $this->container->get('templating')->renderResponse($template, $templateArgs);
        }

        return parent::registerAction($request);
    }
}
