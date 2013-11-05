<?php

namespace CM\CMBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\Notification;

class DoctrineEventsListener
{
    private $serviceContainer;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserRoutine($object, $em);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            if (array_key_exists('status', $em->getUnitOfWork()->getEntityChangeSet($object)))
            {
                $this->entityUserRoutine($object, $em, true);
            }
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if ($object instanceof EntityUser) {
            $this->entityUserRoutine($object, $em, true, false);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->serviceContainer->get('cm.request_center')->flushNeeded() || $this->serviceContainer->get('cm.notification_center')->flushNeeded()) {
            $args->getEntityManager()->flush();
        }
    }

    private function entityUserRoutine(EntityUser $entityUser, EntityManager $em, $remove = false, $create = true)
    {
        $entity = $entityUser->getEntity();
        $post = $entity->getPost();
        $user = $post->getUser();
        $group = $post->getGroup();
        $page = $post->getPage();
        $entityClassName = new \ReflectionClass(get_class($entity));
        $entityClassName = $entityClassName->getShortName();
        $postClassName = new \ReflectionClass(get_class($post));
        $postClassName = $postClassName->getShortName();

        $requestCenter = $this->serviceContainer->get('cm.request_center');
        $notificationCenter = $this->serviceContainer->get('cm.notification_center');
        
        if ($remove) {
            $requestCenter->removeRequests($user, $entityClassName, $entity->getId());
        }

        if ($create) {
            switch ($entityUser->getStatus()) {
                case EntityUser::STATUS_PENDING:
                    $requestCenter->newRequest(
                        $entityUser->getUser(),
                        $user,
                        $entityClassName,
                        $entity->getId(),
                        $entity,
                        $page,
                        $group
                    );
                    break;
                case EntityUser::STATUS_ACTIVE:
                    $notificationCenter->newNotification(
                        Notification::TYPE_REQUEST_ACCEPTED,
                        $entityUser->getUser(),
                        $user,
                        $postClassName,
                        $post->getId(),
                        $post,
                        $page,
                        $group
                    );
                    break;
                case EntityUser::STATUS_REQUESTED:
                    foreach ($em->getRepository('CMBundle:Entity')->getAdmins($entity->getId()) as $admin) {
                        $requestCenter->newRequest(
                            $admin->getUser(),
                            $entityUser->getUser(),
                            $entityClassName,
                            $entity->getId(),
                            $entity
                        );
                    }
                    break;
            }
        }
    }
}