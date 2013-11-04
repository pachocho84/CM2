<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\Event\LifecycleEventArgs;

class EntityUserListener
{
    public function prePersist(EntityUser $entityUser, LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();

        $post = $entityUser->getEntity()->getPost();
        $user = $post->getUser();
        $group = $post->getGroup();
        $page = $post->getPage();
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
                    $request->setUser($admin->getUser())
                        ->setFromUser($entityUser->getUser())
                        ->setEntity($entity)
                        ->setObject($entityClassName->getShortName())
                        ->setObjectId($entity->getId());
                }
                $em->persist($request);
                break;
        }
    }
}