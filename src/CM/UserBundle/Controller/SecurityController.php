<?php

namespace CM\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class SecurityController extends BaseSecurityController
{
    private $template = 'FOSUserBundle:Security:login.html.twig';
    private $templateArgs = array();

    public function loginAction(Request $request, $template = null, $templateArgs = array())
    {
        if (!is_null($template)) {
            $this->template = $template;
        }
        $this->templateArgs = $templateArgs;

        return parent::loginAction($request);
    }

    protected function renderLogin(array $data)
    {
        if ($this->container->get('security.context')->isGranted('ROLE_USER')) {
            return new RedirectResponse($this->container->get('router')->generate('home'));
        }
        return $this->container->get('templating')->renderResponse($this->template, array_merge($data, $this->templateArgs));
    }
}
