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
use CM\CMBundle\Entity\ImageAlbum;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Entity\Group;
use CM\CMBundle\Entity\Relation;
use CM\CMBundle\Entity\Multimedia;

class DoctrineEventsListener
{
    private $serviceContainer;

    private $flushNeeded = false;
    private $wtf = '';

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
        if (is_null($this->get('security.context'))) {
            return null;
        }
        return $this->get('security.context')->getToken()->getUser();
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserPersistedRoutine($object, $em);
        }
        if ($object instanceof GroupUser) {
            $this->groupUserPersistedRoutine($object, $em);
        }
        if ($object instanceof PageUser) {
            $this->groupUserPersistedRoutine($object, $em);
        }
        if ($object instanceof Comment) {
            $this->commentPersistedRoutine($object, $em);
        }
        if($object instanceof Like) {
            $this->likePersistedRoutine($object, $em);
        } 
        if ($object instanceof Fan) {
            $this->fanPersistedRoutine($object, $em);
        }
        if ($object instanceof Image) {
            $this->imagePersistedRoutine($object, $em);
        }
        if (($object instanceof User || $object instanceof Page || $object instanceof Group)
            && ($object->getImg() || $object->getCoverImg() || (property_exists($publisher, 'backgroundImg') && $publisher->getBackgroundImg()))) {
            $this->imgPersistedRoutine($object, $em);
        }
        if ($object instanceof Relation && !$object->getAccepted()) {
            $this->relationPersistedRoutine($object, $em);
        }
        if ($object instanceof Multimedia && !is_null($object->getEntity())) {
            $this->entityMultimediaPersistedRoutine($object, $em);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {      
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityTranslation && $object->getEntity() instanceof Biography) {   
            $this->biographyUpdatedRoutine($object->getEntity(), $em);
        }
        if ($object instanceof Fan) {
            $this->fanUpdatedRoutine($object, $em);
        }
        if ($object instanceof ImageAlbum) {
            $this->imageAlbumUpdatedRoutine($object, $em);
        }
        if (($object instanceof User || $object instanceof Page || $object instanceof Group)
            && (array_key_exists('img', $em->getUnitOfWork()->getEntityChangeSet($object)) 
                || array_key_exists('cover_img', $em->getUnitOfWork()->getEntityChangeSet($object)) 
                || array_key_exists('background_img', $em->getUnitOfWork()->getEntityChangeSet($object)))) {
            $this->imgUpdatedRoutine($object, $em);
        }
        if ($object instanceof Relation && $object->getAccepted()) {
            $this->relationUpdatedRoutine($object, $em);
        }
        if ($object instanceof User && !is_null($this->get('security.context')->getToken())) {
            $this->get('cm.user_authentication')->updateProfile();
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof Page) {
            $this->pagePersistedRoutine($object, $em);
        }
        if ($object instanceof Group) {
            $this->groupPersistedRoutine($object, $em);
        }
        if ($object instanceof EntityUser) {
            $this->entityUserRemovedRoutine($object, $em);
        }
        if ($object instanceof GroupUser) {
            $this->groupUserRemovedRoutine($object, $em);
        }
        if ($object instanceof PageUser) {
            $this->pageUserRemovedRoutine($object, $em);
        }
        if ($object instanceof Comment) {
            $this->commentRemovedRoutine($object, $em);
        }
        if ($object instanceof Like) {
            $this->likeRemovedRoutine($object, $em);
        }
        if ($object instanceof EntityTranslation && $object->getEntity() instanceof Biography) {
            $this->biographyRemovedRoutine($object->getEntity(), $em);
        }
        if ($object instanceof Fan) {
            $this->fanRemovedRoutine($object, $em);
        }
        if ($object instanceof ImageAlbum) {
            // $this->imageAlbumRemovedRoutine($object, $em);
        }
        if ($object instanceof User || $object instanceof Page || $object instanceof Group) {
            $this->imgRemovedRoutine($object, $em);
        }
        if ($object instanceof Relation) {
            $this->relationRemovedRoutine($object, $em);
        }
        if ($object instanceof Multimedia) {
            $this->entityMultimediaRemovedRoutine($object, $em);
        }
    }

    public function postFlush(PostFlushEventArgs $args)
    {
        if ($this->get('cm.request_center')->flushNeeded()
            || $this->get('cm.notification_center')->flushNeeded()
            || $this->get('cm.post_center')->flushNeeded()
            || $this->flushNeeded
        ) {
            $this->get('cm.request_center')->flushed();
            $this->get('cm.notification_center')->flushed();
            $this->get('cm.post_center')->flushed();
            $this->flushNeeded = false;

            $args->getEntityManager()->flush();
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
                    null,
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
        if (!is_null($comment->getPost())) {
            $post = $comment->getPost();
            $entity = $post->getEntity();
            $object = $post;
            $toUser = $post->getUser();
            $toCreator = $post->getCreator();
        } else {
            $post = null;
            $entity = null;
            $object = $comment->getImage();
            $toUser = $object->getUser();
            $toCreator = $object->getUser();
        }

        $this->get('cm.post_center')->newPost(
            $comment->getUser(),
            $comment->getUser(),
            Post::TYPE_CREATION,
            get_class($comment),
            array($comment->getId()),
            $entity
        );

        $notifiedUserIds = array($comment->getUser()->getID());

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_COMMENT,
            $toUser,
            $comment->getUser(),
            get_class($comment),
            $comment->getId(),
            $post
        );
        $notifiedUserIds[] = $toUser->getId();

        if ($toCreator->getId() != $toUser->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_COMMENT,
                $toCreator,
                $comment->getUser(),
                get_class($comment),
                $comment->getId(),
                $post
            );
            $notifiedUserIds[] = $toCreator->getId();
        }

        foreach (array_merge($object->getComments()->toArray(), $object->getLikes()->toArray()) as $toNotify) {
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
        if (!is_null($like->getPost())) {
            $post = $like->getPost();
            $entity = $post->getEntity();
            $object = $post;
            $toUser = $post->getUser();
            $toCreator = $post->getCreator();
        } else {
            $post = null;
            $entity = null;
            $object = $like->getImage();
            $toUser = $object->getUser();
            $toCreator = $object->getUser();
        }

        $this->get('cm.post_center')->newPost(
            $like->getUser(),
            $like->getUser(),
            Post::TYPE_CREATION,
            get_class($like),
            array($like->getId()),
            $entity
        );

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_LIKE,
            $toUser,
            $like->getUser(),
            get_class($like),
            $like->getId(),
            $post
        );

        if ($toCreator->getId() != $toUser->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_LIKE,
                $toCreator,
                $like->getUser(),
                get_class($like),
                $like->getId(),
                $post
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

    private function biographyUpdatedRoutine(Biography $biography, EntityManager $em)
    {
        $post = $biography->getLastPost();

        if ($post->getUpdatedAt()->diff(new \DateTime('now'))->d < 1) {
            $post->setUpdatedAt(new \DateTime);
            $em->persist($post);
        } else {
            $post = $this->get('cm.post_center')->newPost(
                $this->getUser(),
                $this->getUser(),
                Post::TYPE_UPDATE,
                get_class($biography),
                array($biography->getId()),
                $biography
            );
            $biography->addPost($post);
            $em->persist($biography);
        }
        $this->flushNeeded = true;
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

    private function imagePersistedRoutine(Image $image, EntityManager $em)
    {
        $entity = $image->getEntity();
        $user = $image->getUser();
        $page = $image->getPage();
        $group = $image->getGroup();

        if (!is_null($entity) && is_null($entity->getId())) {
            return;
        }

        $post = $em->getRepository('CMBundle:Post')->getLastPosts(array(
            'entityId' => $entity->getId(),
            'object' => get_class($image),
            'userId' => is_null($user) ? null : $user->getId(),
            'groupId' => is_null($group) ? null : $group->getId(),
            'pageId' => is_null($page) ? null : $page->getId(),
            'after' => new \DateTime('-12 hours'),
            'paginate' => false,
            'limit' => 1
        ));

        if (count($post) >= 1) {
            $post = $post[0];
            $post->addObjectId($image->getId());
        } else {
            $post = $this->get('cm.post_center')->getNewPost($user, $image->getUser());
            $post->setPage($image->getPage())
                ->setGroup($image->getGroup())
                ->setObject(get_class($image))
                ->addObjectId($image->getId());

            $entity->addPost($post, false);
        }

        $this->flushNeeded = true;
    }

    public function imageAlbumUpdatedRoutine(ImageAlbum $album, EntityManager $em)
    {
        $post = $em->getRepository('CMBundle:Post')->getLastPosts(array(
            'entityId' => $album->getId(),
            'after' => new \DateTime('-12 hours'),
            'paginate' => false,
            'limit' => 1
        ));
        if (count($post) >= 1) {
            $post = $post[0];
            $post->setUpdatedAt(new \DateTime);
            $em->persist($post);
        } else {
            $creationPost = $album->getPost();

            $post = $this->get('cm.post_center')->getNewPost($user, $creationPost->getUser(), Post::TYPE_UPDATE);
            $post->setPage($creationPost->getPage());
            $post->setGroup($creationPost->getGroup());

            $album->addPost($post);
        }

        $em->persist($album);

        $this->flushNeeded = true;
    }

    private function imgPersistedRoutine($publisher, EntityManager $em)
    {
        $user = null;
        if ($publisher instanceof User) {
            $user = $publisher;
        }
        $page = null;
        if ($publisher instanceof Page) {
            $page = $publisher;
            $user = is_null($this->get('security.context')->getToken()) ? $page->getCreator() : $this->getUser();
        }
        $group = null;
        if ($publisher instanceof Group) {
            $group = $publisher;
            $user = is_null($this->get('security.context')->getToken()) ? $group->getCreator() : $this->getUser();
        }


        if ($publisher->getImg()) {
            $album = new ImageAlbum;
            $album->setType(ImageAlbum::TYPE_PROFILE);
            
            $em->persist($album);

            $image = new Image;
            $image->setImg($publisher->getImg())
                ->setImgOffset($publisher->getImgOffset())
                ->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);
            $post->setPage($page);
            $post->setGroup($group);

            $album->addPost($post);
        }
        if ($publisher->getCoverImg()) {
            $album = new ImageAlbum;
            $album->setType(ImageAlbum::TYPE_COVER);

            $em->persist($album);

            $image = new Image;
            $image->setImg($publisher->getCoverImg())
                ->setImgOffset($publisher->getCoverImgOffset())
                ->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);
            $post->setPage($page);
            $post->setGroup($group);

            $album->addPost($post);
        }
        if (property_exists($publisher, 'backgroundImg') && $publisher->getBackgroundImg()) {
            $album = new ImageAlbum;
            $album->setType(ImageAlbum::TYPE_BACKGROUND);

            $em->persist($album);

            $image = new Image;
            $image->setImg($publisher->getBackgroundImg())
                ->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);
            $post->setPage($page);
            $post->setGroup($group);

            $album->addPost($post);
        }

        $this->flushNeeded = true;
    }

    private function imgUpdatedRoutine($publisher, EntityManager $em)
    {
        $user = null;
        if ($publisher instanceof User) {
            $user = $publisher;
        }
        $page = null;
        if ($publisher instanceof Page) {
            $page = $publisher;
        }
        $group = null;
        if ($publisher instanceof Group) {
            $group = $publisher;
        }

        if (array_key_exists('img', $em->getUnitOfWork()->getEntityChangeSet($publisher))) {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getImageAlbum(array(
                'userId' => is_null($user) ? null : $user->getId(),
                'groupId' => is_null($group) ? null : $group->getId(),
                'pageId' => is_null($page) ? null : $page->getId(),
                'type' => ImageAlbum::TYPE_PROFILE,
            ));
            if (is_null($album)) {
                $album = new ImageAlbum;
                $album->setType(ImageAlbum::TYPE_PROFILE);
            }

            $image = new Image;
            $image->setImg($publisher->getImg())
                ->setImgOffset($publisher->getImgOffset())
                ->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $em->persist($album);
        }
        if (array_key_exists('cover_img', $em->getUnitOfWork()->getEntityChangeSet($publisher))) {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getImageAlbum(array(
                'userId' => is_null($user) ? null : $user->getId(),
                'groupId' => is_null($group) ? null : $group->getId(),
                'pageId' => is_null($page) ? null : $page->getId(),
                'type' => ImageAlbum::TYPE_COVER,
            ));
            if (is_null($album)) {
                $album = new ImageAlbum;
                $album->setType(ImageAlbum::TYPE_COVER);
            }

            $image = new Image;
            $image->setImg($publisher->getCoverImg())
                ->setImgOffset($publisher->getCoverImgOffset())
                ->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $em->persist($album);
        }
        if (array_key_exists('background_img', $em->getUnitOfWork()->getEntityChangeSet($publisher))) {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getImageAlbum(array(
                'userId' => is_null($user) ? null : $user->getId(),
                'groupId' => is_null($group) ? null : $group->getId(),
                'pageId' => is_null($page) ? null : $page->getId(),
                'type' => ImageAlbum::TYPE_BACKGROUND,
            ));
            if (is_null($album)) {
                $album = new ImageAlbum;
                $album->setType(ImageAlbum::TYPE_BACKGROUND);
            }

            $image = new Image;
            $image->setImg($publisher->getBackgroundImg())
                ->setMain(true)
                ->setUser($user)
                ->setPage($page)
                ->setGroup($group);
            $album->addImage($image);

            $em->persist($album);
        }

        $this->flushNeeded = true;
    }

    private function imgRemovedRoutine($publisher, EntityManager $em)
    {
        $this->get('cm.post_center')->removePost(
            $this->getUser(),
            $this->getUser(),
            get_class($biography),
            array($biography->getId())
        );
    }

    private function relationPersistedRoutine(Relation $relation, EntityManager $em)
    {
        $inverse = new Relation;
        $inverse->setAccepted(true)
            ->setType($relation->getInverseType())
            ->setFromUser($relation->getUser())
            ->setUser($relation->getFromUser());
        $em->persist($inverse);

        $this->flushNeeded = true;

        $this->get('cm.request_center')->newRequest(
            $inverse->getUser(),
            $inverse->getFromUser(),
            get_class($inverse),
            $relation->getId()
        );
    }

    private function relationUpdatedRoutine(Relation $relation, EntityManager $em)
    {
        $post = $this->get('cm.post_center')->newPost(
            $relation->getFromUser(),
            $relation->getFromUser(),
            Post::TYPE_CREATION,
            get_class($relation),
            array($relation->getId())
        );

        $inverse = $em->getRepository('CMBundle:Relation')->getInverse($relation->getType(), $relation->getUserId(), $relation->getFromUserId());

        $post = $this->get('cm.post_center')->newPost(
            $inverse->getFromUser(),
            $inverse->getFromUser(),
            Post::TYPE_CREATION,
            get_class($inverse),
            array($inverse->getId())
        );
    }

    private function relationRemovedRoutine(Relation $relation, EntityManager $em)
    {
        try {
            $this->get('cm.post_center')->removePost(
                $relation->getFromUser(),
                $relation->getFromUser(),
                get_class($relation),
                array($relation->getId())
            );
        } catch (\Exception $e) {
        }
    }

    private function entityMultimediaPersistedRoutine(Multimedia $multimedia, EntityManager $em)
    {
        $post = $this->get('cm.post_center')->getNewPost(
            is_null($this->get('security.context')->getToken()) ? $multimedia->getEntity()->getPost()->getCreator() : $this->getUser(),
            is_null($this->get('security.context')->getToken()) ? $multimedia->getEntity()->getPost()->getUser() : $this->getUser(),
            Post::TYPE_CREATION,
            get_class($multimedia),
            array($multimedia),
            $multimedia->getEntity()
        );
    }

    private function entityMultimediaRemovedRoutine(Multimedia $multimedia, EntityManager $em)
    {
        try {
            $this->get('cm.post_center')->removePost(
                null, null,
                get_class($multimedia),
                array($multimedia->getId())
            );
        } catch (\Exception $e) {
        }
    }
}