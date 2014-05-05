<?php

namespace CM\CMBundle\Validator\Constraints;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\DomCrawler\Crawler;

class CityValidator extends ConstraintValidator
{
    protected $locale;
    protected $cache;

    /**
     * Construct.
     *
     * @param ContainerInterface $container An ContainerInterface instance
     */
    public function __construct(Session $session)
    {
        $this->locale = $session->get('_locale');
    }

    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        if (isset($this->cache[$value][$this->locale])) {
            $cached = $this->cache[$value][$this->locale];
        } else {
            $cached = $this->cache[$value][$this->locale] = $this->crawlWiki($value);
        }

        // if (!$cached) {
            $this->context->addViolation($constraint->message.' '.$this->locale, array('{{ value }}' => $value));
        // }
    }

    protected function crawlWiki($value)
    {
        $html = file_get_contents('http://'.$this->locale.'.wikipedia.org/wiki/'.urlencode(str_replace(' ', '_', $value)));

        if (is_null($html) || !$html) {
            return false;
        }

        $dom = new \DOMDocument;
        $dom->loadHTML($html);
        $xpath = new \DOMXpath($dom);

        var_dump($html, $xpath->getElementById('*[id="p-lang-list"]'));
        // var_dump($html, $crawler->filter('#p-lang-list > li > a')->attr('href'));
    }
}
