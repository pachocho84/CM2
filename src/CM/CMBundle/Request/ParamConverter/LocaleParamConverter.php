<?php

namespace CM\CMBundle\Request\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Locale;

class LocaleParamConverter implements ParamConverterInterface
{
    public function apply(Request $request, ConfigurationInterface $configuration)
    {
        $_locale = $request->get('_locale');

        $locale = new Locale($_locale);

        if ($locale->notFound()) {
            throw new NotFoundHttpException();
        }

        $param = $configuration->getName();
        $request->set($param, $locale);
        $request->setLocale($locale);

        return true;
    }

    public function supports(ConfigurationInterface $configuration)
    {
        return "CM\CMBundle\Entity\Locale" === $configuration->getClass();
    }
}