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
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserRoutine($object, $em);
        } elseif ($object instanceof Comment) {
            $this->commentRoutine($object, $em);
        } elseif($object instanceof Like) {
            $this->likeRoutine($object, $em);
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

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserRoutine($object, $em, true, false);
        } elseif ($object instanceof Comment) {
            $this->commentRemovedRoutine($object, $em);
        } elseif ($object instanceof Like) {
            $this->likeRemovedRoutine($object, $em);
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

        $requestCenter = $this->get('cm.request_center');
        $notificationCenter = $this->get('cm.notification_center');
        
        if ($remove) {
            $requestCenter->removeRequests($user, get_class($entity), $entity->getId());
        }

        if ($create) {
            switch ($entityUser->getStatus()) {
                case EntityUser::STATUS_PENDING:
                    $requestCenter->newRequest(
                        $entityUser->getUser(),
                        $user,
                        get_class($entity),
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
                        get_class($post),
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
                            get_class($entity),
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
        $this->get('cm.post_center')->newPost(
            $comment->getUser(),
            $comment->getUser(),
            Post::TYPE_CREATION,
            get_class($comment),
            array($comment->getId()),
            $comment->getPost()->getEntity()
        );

        $notifiedUserIds = array($comment->getUser()->getID());

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_COMMENT,
            $comment->getPost()->getUser(),
            $comment->getUser(),
            get_class($comment),
            $comment->getId(),
            $comment->getPost()
        );
        $notifiedUserIds[] = $comment->getPost()->getUser()->getId();

        if ($comment->getPost()->getCreator()->getId() != $comment->getPost()->getUser()->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_COMMENT,
                $comment->getPost()->getCreator(),
                $comment->getUser(),
                get_class($comment),
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
                get_class($comment),
                $comment->getId(),
                $comment->getPost()
            );
        }
    }

    private function commentRemovedRoutine(Comment $comment, EntityManager $em)
    {
        $this->get('cm.post_center')->removePost(
            $comment->getUser(),
            $comment->getUser(),
            get_class($comment),
            array($comment->getId())
        );

        $this->get('cm.notification_center')->removeNotifications($comment->getUser()->getId(), get_class($comment), $comment->getId(), Notification::TYPE_COMMENT);
    }

    private function likeRoutine(Like $like, EntityManager $em)
    {
        $this->get('cm.post_center')->newPost(
            $like->getUser(),
            $like->getUser(),
            Post::TYPE_CREATION,
            get_class($like),
            array($like->getId()),
            $like->getPost()->getEntity()
        );

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_LIKE,
            $like->getPost()->getUser(),
            $like->getUser(),
            get_class($like),
            $like->getId(),
            $like->getPost()
        );

        if ($like->getPost()->getCreator()->getId() != $like->getPost()->getUser()->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_LIKE,
                $like->getPost()->getCreator(),
                $like->getUser(),
                get_class($like),
                $like->getId(),
                $like->getPost()
            );
        }
    }

    private function likeRemovedRoutine(Like $like, EntityManager $em)
    {
        $this->get('cm.post_center')->removePost(
            $like->getUser(),
            $like->getUser(),
            get_class($like),
            array($like->getId())
        );

        $this->get('cm.notification_center')->removeNotifications($like->getUser()->getId(), get_class($like), $like->getId(), Notification::TYPE_LIKE);
    }
}