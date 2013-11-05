<?php

namespace CM\CMBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Request;

// TODO: hopefully they will add entityListeners as a doctrine annotation in symfony
class DoctrineEventsListener
{
    private $flushNeeded = false;

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
            $entityUser = $object;
            $entity = $entityUser->getEntity();
            $post = $entity->getPost();
            $user = $post->getUser();
            $group = $post->getGroup();
            $page = $post->getPage();
            $entityClassName = new \ReflectionClass(get_class($entity));
            $entityClassName = $entityClassName->getShortName();
            
            switch ($entityUser->getStatus()) {
                case EntityUser::STATUS_PENDING:
                    $this->serviceContainer->get('cm.request_center')->newRequest(
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
                    // TODO: notificate
                    $this->serviceContainer->get('cm.notification_center')->newNotification(
                        'test',
                        $entityUser->getUser(),
                        $user,
                        $post,
                        $page,
                        $group
                    );
                    break;
                case EntityUser::STATUS_REQUESTED:
                    foreach ($em->getRepository('CMBundle:Entity')->getAdmins($entity->getId()) as $admin) {
                        $this->serviceContainer->get('cm.request_center')->newRequest(
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

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {

            $entityUser = $object;
            $entity = $entityUser->getEntity();
            $post = $entity->getPost();
            $user = $post->getUser();
            $group = $post->getGroup();
            $page = $post->getPage();
            $entityClassName = new \ReflectionClass(get_class($entity));
            $entityClassName = $entityClassName->getShortName();

            if (array_key_exists('status', $em->getUnitOfWork()->getEntityChangeSet($object)))
            {
                $this->serviceContainer->get('cm.request_center')->removeRequests($user, $entityClassName, $entity->getId());
                $this->serviceContainer->get('cm.request_center')->newRequest(
                    $entityUser->getUser(),
                    $user,
                    $entityClassName,
                    $entity->getId(),
                    $entity,
                    $page,
                    $group
                );
            }
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if ($object instanceof EntityUser) {

            $entityUser = $object;
            $post = $entityUser->getEntity()->getPost();
            $user = $post->getUser();
            $entity = $entityUser->getEntity();
            $entityClassName = new \ReflectionClass(get_class($entity));
            $entityClassName = $entityClassName->getShortName();

            $this->serviceContainer->get('cm.request_center')->removeRequests($user, $entityClassName, $entity->getId());
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        $this->serviceContainer->get('cm.request_center')->flush();
    }
}