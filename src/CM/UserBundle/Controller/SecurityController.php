<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CM\UserBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Controller\SecurityController as BaseSecurityController;

class SecurityController extends BaseSecurityController
{
    private $template = 'FOSUserBundle:Security:login.html.twig';

    public function loginAction(Request $request, $template = null)
    {
        if (!is_null($template)) {
         $this->template = $template;
        }

        return parent::loginAction($request);
    }

    protected function renderLogin(array $data)
    {
        return $this->container->get('templating')->renderResponse($this->template, $data);
    }
}
