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
            case 'Group':
                return 'CM\CMBundle\Entity\Group';
            default:
                throw new \Exception('add class name '.$shortName);
        }
    }

    function getObject($object, $objectId, $container = null)
    {
        switch (self::className($object))
        {
            case 'Image':
                return $this->em->getRepository('CMBundle:Image')->getImagesByIds($objectId, array('limit' => 6));
            case 'Comment':
                return $this->em->getRepository('CMBundle:Comment')->findOneById($objectId);
            case 'Like':
                return $this->em->getRepository('CMBundle:Like')->findOneById($objectId);
            case 'Fan':
                return $this->em->getRepository('CMBundle:Fan')->getFans($objectId);
            case 'Group':
                return null;
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
            case 'Entity':
            case 'Event':
            case 'ImageAlbum':
            case 'Disc':
            case 'Article':
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
}