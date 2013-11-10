<?php

namespace CM\CMBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\Post;

class DoctrineEventsListener
{
    private $serviceContainer;

    private $flushNeeded = false;

    public function __construct(ContainerInterface $serviceContainer)
    {
        $this->serviceContainer = $serviceContainer;
    }

    private function get($service)
    {
        return $this->serviceContainer->get($service);
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $like = $args->getEntity();
        $em = $args->getEntityManager();

        if ($like instanceof EntityUser) {
            $this->entityUserRoutine($like, $em);
        } elseif ($like instanceof Comment) {
            $this->commentRoutine($like, $em);
        } elseif($like instanceof Like) {
            $this->likeRoutine($like, $em);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $like = $args->getEntity();
        $em = $args->getEntityManager();

        if ($like instanceof EntityUser) {
            if (array_key_exists('status', $em->getUnitOfWork()->getEntityChangeSet($like)))
            {
                $this->entityUserRoutine($like, $em, true);
            }
        }
    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $like = $args->getEntity();

        if ($like instanceof EntityUser) {
            $this->entityUserRoutine($like, $em, true, false);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->get('cm.request_center')->flushNeeded()
            || $this->get('cm.notification_center')->flushNeeded()
            || $this->get('cm.post_center')->flushNeeded()
            || $this->flushNeeded) {
            $args->getEntityManager()->flush();

            $this->get('cm.request_center')->flushed();
            $this->get('cm.notification_center')->flushed();
            $this->get('cm.post_center')->flushed();
            $this->flushNeeded = false;
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

        $requestCenter = $this->get('cm.request_center');
        $notificationCenter = $this->get('cm.notification_center');
        
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

    private function commentRoutine(Comment $comment, EntityManager $em)
    {
        $className = new \ReflectionClass(get_class($comment));
        $className = $className->getShortName();

        $this->get('cm.post_center')->newPost(
            $comment->getUser(),
            $comment->getUser(),
            Post::TYPE_CREATION,
            $className,
            array($comment->getId())
        );

        $notifiedUserIds = array($comment->getUser()->getID());

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_LIKE,
            $comment->getPost()->getUser(),
            $comment->getUser(),
            $className,
            $comment->getId(),
            $comment->getPost()
        );
        $notifiedUserIds[] = $comment->getPost()->getUser()->getId();

        if ($comment->getPost()->getCreator()->getId() != $comment->getPost()->getUser()->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_COMMENT,
                $comment->getPost()->getCreator(),
                $comment->getUser(),
                $className,
                $comment->getId(),
                $comment->getPost()
            );
            $notifiedUserIds[] = $comment->getPost()->getCreator()->getId();
        }

        foreach (array_merge($comment->getPost()->getComments()->toArray(), $comment->getPost()->getLikes()->toArray()) as $toNotify) {
            if (in_array($toNotify->getUser()->getId(), $notifiedUserIds)) {
                continue;
            }
            $notifiedUserIds[] = $toNotify->getUser()->getId();

            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_COMMENT,
                $toNotify->getUser(),
                $comment->getUser(),
                $className,
                $comment->getId(),
                $comment->getPost()
            );
        }
    }

    private function likeRoutine(Like $like, EntityManager $em)
    {
        $className = new \ReflectionClass(get_class($like));
        $className = $className->getShortName();

        $this->get('cm.post_center')->newPost(
            $like->getUser(),
            $like->getUser(),
            Post::TYPE_CREATION,
            $className,
            array($like->getId())
        );

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_LIKE,
            $like->getPost()->getUser(),
            $like->getUser(),
            $className,
            $like->getId(),
            $like->getPost()
        );

        if ($like->getPost()->getCreator()->getId() != $like->getPost()->getUser()->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_LIKE,
                $like->getPost()->getCreator(),
                $like->getUser(),
                $className,
                $like->getId(),
                $like->getPost()
            );
        }
    }
}