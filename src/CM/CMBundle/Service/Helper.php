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

    function className($object)
    {
        $name = new \ReflectionClass(get_class($object));
        return $name->getShortName();
    }

    function getObject($object, $object_id)
    {
        switch (strtolower($object))
        {
            case 'entity':
                return $this->em->getRepository('CMBundle:Post')->getEntity($objectId);
                return PostQuery::create()->joinWithEntity()->where('Entity.Id = ?', $object_id)->findOne();
            case 'event':
                return $this->em->getRepository('CMBundle:Post')->getEntity($objectId);
                return PostQuery::create()->joinWithEntity()->useEntityQuery()->joinWithEvent()->endUse()->findOneById($object_id);
            // case 'disc':
            //     return PostQuery::create()->joinWithEntity()->useEntityQuery()->joinWithDisc()->endUse()->findOneById($object_id);
            // case 'article':
            //     return PostQuery::create()->joinWithEntity()->useEntityQuery()->joinWithArticle()->endUse()->findOneById($object_id);
            // case 'image':
            //     return is_array($object_id) ? ImageQuery::create()->filterById($object_id)->orderByCreatedAt('desc')->find() : ImageQuery::create()->filterById($object_id)->findOne();
            // case 'link':
            //     return PostQuery::create()->joinWithEntity()->useEntityQuery()->joinWithLink()->endUse()->findOneById($object_id);
            // case 'user':
            //     return is_array($object_id) ? UserQuery::create()->filterById($object_id)->find() : UserQuery::create()->filterById($object_id)->findOne();
            // case 'page':
            //     return is_array($object_id) ? PageQuery::create()->filterById($object_id)->find() : PageQuery::create()->filterById($object_id)->findOne();
            // case 'group':
            //     return is_array($object_id) ? GroupQuery::create()->filterById($object_id)->find() : GroupQuery::create()->filterById($object_id)->findOne();
            // case 'education':
            //     return is_array($object_id) ? EducationQuery::create()->filterById($object_id)->orderByDateTo('desc')->find() : EducationQuery::create()->filterById($object_id)->findOne();
            // case 'job':
            //     return is_array($object_id) ? JobQuery::create()->filterById($object_id)->orderByDateTo('desc')->find() : JobQuery::create()->filterById($object_id)->findOne();
            // case 'education_masterclass':
            //     return is_array($object_id) ? EducationMasterclassQuery::create()->filterById($object_id)->orderByDateTo('desc')->find() : EducationMasterclassQuery::create()->filterById($object_id)->findOne();
            // default:
            //     return false;
        }
    }
}