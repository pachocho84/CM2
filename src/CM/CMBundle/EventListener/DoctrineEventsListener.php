<?php

namespace CM\CMBundle\EventListener;

use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Request;

class DoctrineEventsListener
{
    public function onPostPersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        $em = $args->getObjectManager();
               
        if ($entityUser = $object instanceof EntityUser && $entityUser->isNew()) {
            $post = $entityUser->getEntity()->getPost();
            $user = $post->getUser();
            $group = $post->getGroup();
            $page = $post->getPost();
            $entity = $entityUser->getEntity();
            $entityClassName = new \ReflectionClass(get_class($entity));
            
            switch ($entityUser->getStatus()) {
                case EntityUser::STATUS_PENDING:
                    $request = new Request;
                    $request->setUser($entityUser->getUser())
                        ->setFromUser($user)
                        ->setEntity($entity)
                        ->setObject($entityClassName->getShortName())
                        ->setObjectId($entity->getId());
                    if (!is_null($page)) {
                        $request->setPage($page);
                    } elseif (!is_null($group)) {
                        $request->setGroup($group);
                    }
                    $em->persist($request);
                    break;
                case EntityUser::STATUS_ACTIVE:
                    // TODO: notificate
                    break;
                case EntityUser::STATUS_REQUESTED:
                    foreach ($em->getRepository('CMBundle:Entity')->getAdmins($entity->getId()) as $admin) {
                        $request = new Request;
                        $request->setUser($admin)
                            ->setFromUser($entityUser->getUser())
                            ->setEntity($entity)
                            ->setObject($entityClassName->getShortName())
                            ->setObjectId($entity->getId());
                    }
                    break;
            }
        }
    }
}