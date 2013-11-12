<?php

namespace CM\CMBundle\Service;

use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\EntityUser;

class Helper
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public static function className($object)
    {
        $name = new \ReflectionClass(is_string($object) ? $object : get_class($object));
        return $name->getShortName();
    }

    public static function fullClassName($shortName)
    {
        switch ($shortName) {
            case 'Entity':
                return 'CM\CMBundle\Entity\Entity';
            case 'Event':
                return 'CM\CMBundle\Entity\Event';
            default:
                throw new \Exception('add class name '.$shortName);
        }
    }

    function getObject($object, $objectId)
    {
        switch (self::className($object))
        {
            case 'Entity':
            case 'Event':
            case 'Disc':
            case 'Article':
                return $this->em->getRepository('CMBundle:Post')->getEntity($objectId);
            case 'Comment':
                return $this->em->getRepository('CMBundle:Comment')->findOneById($objectId);
            case 'Like':
                return $this->em->getRepository('CMBundle:Like')->findOneById($objectId);
            case 'image':
                // return is_array($object_id) ? ImageQuery::create()->filterById($object_id)->orderByCreatedAt('desc')->find() : ImageQuery::create()->filterById($object_id)->findOne();
            case 'link':
                // return PostQuery::create()->joinWithEntity()->useEntityQuery()->joinWithLink()->endUse()->findOneById($object_id);
            case 'user':
                // return is_array($object_id) ? UserQuery::create()->filterById($object_id)->find() : UserQuery::create()->filterById($object_id)->findOne();
            case 'page':
                // return is_array($object_id) ? PageQuery::create()->filterById($object_id)->find() : PageQuery::create()->filterById($object_id)->findOne();
            case 'group':
                // return is_array($object_id) ? GroupQuery::create()->filterById($object_id)->find() : GroupQuery::create()->filterById($object_id)->findOne();
            case 'education':
                // return is_array($object_id) ? EducationQuery::create()->filterById($object_id)->orderByDateTo('desc')->find() : EducationQuery::create()->filterById($object_id)->findOne();
            case 'job':
                // return is_array($object_id) ? JobQuery::create()->filterById($object_id)->orderByDateTo('desc')->find() : JobQuery::create()->filterById($object_id)->findOne();
            case 'education_masterclass':
                // return is_array($object_id) ? EducationMasterclassQuery::create()->filterById($object_id)->orderByDateTo('desc')->find() : EducationMasterclassQuery::create()->filterById($object_id)->findOne();
            default:
                return null;
        }
    }

    /**
     * Returns subject replaced with regular expression matchs
     *
     * @param mixed $search        subject to search
     * @param array $replacePairs  array of search => replace pairs
     */
    public static function pregtr($search, $replacePairs)
    {
        return preg_replace(array_keys($replacePairs), array_values($replacePairs), $search);
    }

    /**
     * Truncates +text+ to the length of +length+ and replaces the last three characters with the +truncate_string+
     * if the +text+ is longer than +length+.
     */
    public static function truncate_text($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
    {
        if ($text == '')
        {
            return '';
        }
      
        $mbstring = extension_loaded('mbstring');
        if($mbstring)
        {
           $old_encoding = mb_internal_encoding();
           @mb_internal_encoding(mb_detect_encoding($text));
        }
        $strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
        $substr = ($mbstring) ? 'mb_substr' : 'substr';
      
        if ($strlen($text) > $length)
        {
            $truncate_text = $substr($text, 0, $length - $strlen($truncate_string));
            if ($truncate_lastspace)
            {
              $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
            }
            $text = $truncate_text.$truncate_string;
        }
      
        if ($mbstring)
        {
           @mb_internal_encoding($old_encoding);
        }
      
        return $text;
    }

    /**
     * Returns +text+ transformed into html using very simple formatting rules
     * Surrounds paragraphs with <tt>&lt;p&gt;</tt> tags, and converts line breaks into <tt>&lt;br /&gt;</tt>
     * Two consecutive newlines(<tt>\n\n</tt>) are considered as a paragraph, one newline (<tt>\n</tt>) is
     * considered a linebreak, three or more consecutive newlines are turned into two newlines
     */
    public static function simple_format_text($text, $options = array())
    {
        $css = (isset($options['class'])) ? ' class="'.$options['class'].'"' : '';

        $text = self::pregtr($text, array(
            "/(\r\n|\r)/" => "\n",            // lets make them newlines crossplatform
            "/\n{2,}/"    => "</p><p$css>" // turn two and more newlines into paragraph
        ));

        // turn single newline into <br/>
        $text = str_replace("\n", "\n<br />", $text);
        return '<p'.$css.'>'.$text.'</p>'; // wrap the first and last line in paragraphs before we're done
    }

    public static function show_text($text)
    {
        $actual_length = strlen($text);
        $stripped_length = strlen(strip_tags($text));

        if ($actual_length != $stripped_length) {
            return $text;
        } else {
            return self::simple_format_text($text);
        }
    }
}