<?php

namespace CM\CMBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\GroupUser;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Request;
use CM\CMBundle\Entity\Notification;
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\Fan;

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

    private function getUser()
    {
        return $this->get('security.context')->getToken()->getUser();
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserPersistedRoutine($object, $em);
        } elseif ($object instanceof GroupUser) {
            $this->groupUserPersistedRoutine($object, $em);
        } elseif ($object instanceof PageUser) {
            $this->groupUserPersistedRoutine($object, $em);
        } elseif ($object instanceof Comment) {
            $this->commentPersistedRoutine($object, $em);
        } elseif($object instanceof Like) {
            $this->likePersistedRoutine($object, $em);
        } elseif ($object instanceof Biography) {
            $this->biographyPersistedRoutine($object, $em);
        } elseif ($object instanceof Fan) {
            $this->fanPersistedRoutine($object, $em);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityTranslation && $object->getEntity() instanceof Biography) {   
            $this->biographyUpdatedRoutine($object->getEntity(), $em);
        } elseif ($object instanceof Fan) {
            $this->fanUpdatedRoutine($object, $em);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserRemovedRoutine($object, $em);
        } elseif ($object instanceof GroupUser) {
            $this->groupUserRemovedRoutine($object, $em);
        } elseif ($object instanceof PageUser) {
            $this->pageUserRemovedRoutine($object, $em);
        } elseif ($object instanceof Comment) {
            $this->commentRemovedRoutine($object, $em);
        } elseif ($object instanceof Like) {
            $this->likeRemovedRoutine($object, $em);
        } elseif ($object instanceof EntityTranslation && $object->getEntity() instanceof Biography) {
            $this->biographyRemovedRoutine($object->getEntity(), $em);
        } elseif ($object instanceof Fan) {
            $this->fanRemovedRoutine($object, $em);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->get('cm.request_center')->flushNeeded()
            || $this->get('cm.notification_center')->flushNeeded()
            || $this->get('cm.post_center')->flushNeeded()
            || $this->flushNeeded
        ) {
            $args->getEntityManager()->flush();

            $this->get('cm.request_center')->flushed();
            $this->get('cm.notification_center')->flushed();
            $this->get('cm.post_center')->flushed();
            $this->flushNeeded = false;
        }
    }

    private function entityUserPersistedRoutine(EntityUser $entityUser, EntityManager $em)
    {
        $entity = $entityUser->getEntity();
        $post = $entity->getPost();
        $user = $post->getUser();
        $group = $post->getGroup();
        $page = $post->getPage();

        $requestCenter = $this->get('cm.request_center');
        $notificationCenter = $this->get('cm.notification_center');

        switch ($entityUser->getStatus()) {
            case EntityUser::STATUS_PENDING:
                $requestCenter->newRequest(
                    $entityUser->getUser(),
                    $user,
                    null,
                    null,
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
                    null,
                    null,
                    $post,
                    $page,
                    $group
                );
                break;
            case EntityUser::STATUS_REQUESTED:
                foreach ($em->getRepository('CMBundle:Entity')->getAdmins($entity->getId()) as $admin) {
                    $requestCenter->newRequest(
                        $admin,
                        $entityUser->getUser(),
                        null,
                        null,
                        $entity
                    );
                }
                break;
        }
    }

    private function entityUserRemovedRoutine(EntityUser $entityUser, EntityManager $em)
    {
        $entity = $entityUser->getEntity();
        $post = $entity->getPost();
        $user = $post->getUser();
        $group = $post->getGroup();
        $page = $post->getPage();

        $this->get('cm.request_center')->removeRequest($user, get_class($entity), $entity->getId(), 'sent');
    }

    private function groupUserPersistedRoutine(GroupUser $groupUser, EntityManager $em)
    {
        $group = $groupUser->getGroup();
        $post = $group->getPost();
        $user = $post->getUser();
        $page = $post->getPage();

        $requestCenter = $this->get('cm.request_center');
        $notificationCenter = $this->get('cm.notification_center');

        switch ($groupUser->getStatus()) {
            case GroupUser::STATUS_PENDING:
                $requestCenter->newRequest(
                    $groupUser->getUser(),
                    $user,
                    null,
                    null,
                    null,
                    null,
                    $group
                );
                break;
            case GroupUser::STATUS_ACTIVE:
                $notificationCenter->newNotification(
                    Notification::TYPE_REQUEST_ACCEPTED,
                    $groupUser->getUser(),
                    $user,
                    null,
                    nulll,
                    $post,
                    $page,
                    $group
                );
                break;
            case GroupUser::STATUS_REQUESTED:
                foreach ($em->getRepository('CMBundle:Group')->getAdmins($group->getId()) as $admin) {
                    $requestCenter->newRequest(
                        $admin,
                        $groupUser->getUser(),
                        null,
                        null,
                        null,
                        $page,
                        $group
                    );
                }
                break;
        }
    }

    private function groupUserRemovedRoutine(GroupUser $groupUser, EntityManager $em)
    {
        $group = $groupUser->getGroup();
        $post = $group->getPost();
        $user = $post->getUser();
        $page = $post->getPage();

        $this->get('cm.request_center')->removeRequest($user, get_class($group), $group->getId(), 'sent');
    }

    private function pageUserPersistedRoutine(PageUser $pageUser, EntityManager $em)
    {
        $page = $pageUser->getPage();
        $post = $page->getPost();
        $user = $post->getUser();
        $page = $post->getPage();

        $requestCenter = $this->get('cm.request_center');
        $notificationCenter = $this->get('cm.notification_center');

        switch ($pageUser->getStatus()) {
            case PageUser::STATUS_PENDING:
                $requestCenter->newRequest(
                    $pageUser->getUser(),
                    $user,
                    null,
                    null,
                    null,
                    null,
                    $page
                );
                break;
            case PageUser::STATUS_ACTIVE:
                $notificationCenter->newNotification(
                    Notification::TYPE_REQUEST_ACCEPTED,
                    $pageUser->getUser(),
                    $user,
                    null,
                    nulll,
                    $post,
                    $page,
                    $page
                );
                break;
            case PageUser::STATUS_REQUESTED:
                foreach ($em->getRepository('CMBundle:Page')->getAdmins($page->getId()) as $admin) {
                    $requestCenter->newRequest(
                        $admin,
                        $pageUser->getUser(),
                        null,
                        null,
                        null,
                        $page,
                        $page
                    );
                }
                break;
        }
    }

    private function pageUserRemovedRoutine(PageUser $pageUser, EntityManager $em)
    {
        $page = $pageUser->getPage();
        $post = $page->getPost();
        $user = $post->getUser();
        $page = $post->getPage();

        $this->get('cm.request_center')->removeRequest($user, get_class($page), $page->getId(), 'sent');
    }

    private function commentPersistedRoutine(Comment $comment, EntityManager $em)
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

    private function likePersistedRoutine(Like $like, EntityManager $em)
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

    private function biographyPersistedRoutine(Biography $biography, EntityManager $em)
    {
        $this->get('cm.post_center')->newPost(
            $this->getUser(),
            $this->getUser(),
            Post::TYPE_CREATION,
            get_class($biography),
            array($biography->getId()),
            $biography
        );
    }

    private function biographyUpdatedRoutine(Biography $biography, EntityManager $em)
    {
        $post = $biography->getLastPost();

        if ($post->getUpdatedAt()->diff(new \DateTime('now'))->d < 1) {
            $post->setType(Post::TYPE_UPDATE);
            $em->persist($post);
            $em->flush();
        } else {
            $this->get('cm.post_center')->newPost(
                $this->getUser(),
                $this->getUser(),
                Post::TYPE_UPDATE,
                get_class($biography),
                array($biography->getId()),
                $biography
            );
        }
    }

    private function biographyRemovedRoutine(Biography $biography, EntityManager $em)
    {
        $this->get('cm.post_center')->removePost(
            $this->getUser(),
            $this->getUser(),
            get_class($biography),
            array($biography->getId())
        );
    }

    private function fanPersistedRoutine(Fan $fan, EntityManager $em)
    {
        if (!is_null($fan->getUser())) {
            $postType = Post::TYPE_FAN_USER;
        } elseif (!is_null($fan->getPage())) {
            $postType = Post::TYPE_FAN_PAGE;
        } elseif (!is_null($fan->getGroup())) {
            $postType = Post::TYPE_FAN_GROUP;
        }

        $post = $em->getRepository('CMBundle:Post')->getLastPostFor($fan->getFromUser()->getId(), $postType, get_class($fan), null, array('after' => new \DateTime('-12 hours'), 'limit' => 1));

        if (count($post) > 0) {
            $post = $post[0];
            $post->addObjectId($fan->getId());
            $em->persist($post);
            $this->flushNeeded = true;
        } else {
            $post = $this->get('cm.post_center')->newPost(
                $fan->getFromUser(),
                $fan->getFromUser(),
                $postType,
                get_class($fan),
                array($fan->getId())
            );
        }

        $toNotify = array();
        if (!is_null($fan->getUser())) {
            $toNotify[] = $fan->getUser();
        } elseif (!is_null($fan->getPage())) {
            $toNotify = $em->getRepository('CMBundle:Page')->getAdmins($fan->getPage()->getId());
        } elseif (!is_null($fan->getGroup())) {
            $toNotify = $em->getRepository('CMBundle:Group')->getAdmins($fan->getGroup()->getId());
        }

        foreach ($toNotify as $user) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_FAN,
                $user,
                $fan->getFromUser(),
                get_class($fan),
                $fan->getId(),
                $post
            );
        }
    }

    private function fanRemovedRoutine(Fan $fan, EntityManager $em)
    {
        if (!is_null($fan->getUser())) {
            $postType = Post::TYPE_FAN_USER;
        } elseif (!is_null($fan->getPage())) {
            $postType = Post::TYPE_FAN_PAGE;
        } elseif (!is_null($fan->getGroup())) {
            $postType = Post::TYPE_FAN_GROUP;
        }

        $post = $em->getRepository('CMBundle:Post')->getLastPostFor($fan->getFromUser()->getId(), $postType, get_class($fan), $fan->getId(), array('limit' => 1));

        if (count($post) > 0) {
            $post = $post[0];
            $post->removeObjectId($fan->getId());

            if (count($post->getObjectIds()) == 0) {
                $em->remove($post);
            } else {
                $em->persist($post);
            }
            $this->flushNeeded = true;
        }

        $this->get('cm.notification_center')->removeNotifications($fan->getFromUser()->getId(), get_class($fan), $fan->getId(), Notification::TYPE_FAN);
    }
}