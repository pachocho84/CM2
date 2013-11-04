<?php

namespace CM\CMBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Request;

// TODO: hopefully they will add entityListeners as a doctrine annotation in symfony
class DoctrineEventsListener
{
    private $flushNeeded = false;

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser && $object->isNew()) {
            $entityUser = $object;
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
                    $this->flushNeeded = true;
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
                    $this->flushNeeded = true;
                    break;
            }
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->flushNeeded) {
            $this->flushNeeded = false;
            $args->getEntityManager()->flush();
        }
    }
}