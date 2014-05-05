<?php

namespace CM\CMBundle\EventListener;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\EntityManager;
use CM\CMBundle\Entity\EntityUser;
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
use CM\CMBundle\Entity\PageUser;
use CM\CMBundle\Entity\Relation;
use CM\CMBundle\Entity\Multimedia;

class DoctrineEventsListener
{
    private $container;

    private $flushNeeded = false;
    private $wtf = '';

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function get($service)
    {
        return $this->container->get($service);
    }

    private function getUser()
    {
        if (is_null($this->get('security.context')->getToken())) {
            return null;
        }
        return $this->get('security.context')->getToken()->getUser();
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();

        if ($object instanceof Image || $object instanceof User || $object instanceof Page) {
            $this->imagePersistingOrUpdatingRoutine($object);
        }
    }

    public function preUpdate(LifecycleEventArgs $args)
    {      
        $object = $args->getEntity();

        if ($object instanceof Image || $object instanceof User || $object instanceof Page) {
            $this->imagePersistingOrUpdatingRoutine($object, $args);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        // if ($object instanceof User) {
        //     $this->userPersistedRoutine($object, $em);
        // }
        if ($object instanceof EntityUser) {
            $this->entityUserPersistedRoutine($object, $em);
        }
        if ($object instanceof PageUser) {
            $this->pageUserPersistedRoutine($object, $em);
        }
        if ($object instanceof Post && in_array($object->getObject(), array(Comment::className(), Like::className()))) {
            $this->postAggregatePersistedRoutine($object, $em);
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
        if (($object instanceof User || $object instanceof Page)
            && ($object->getImg() || $object->getCoverImg() || (property_exists($object, 'backgroundImg') && $object->getBackgroundImg()))) {
            $this->imgPersistedRoutine($object, $em);
        }
        if ($object instanceof Relation && $object->getAccepted() == Relation::ACCEPTED_UNI) {
            $this->relationOutPersistedRoutine($object, $em);
        }
        if ($object instanceof Relation && $object->getAccepted() == Relation::ACCEPTED_NO) {
            $this->relationInPersistedRoutine($object, $em);
        }
        if ($object instanceof Multimedia && !is_null($object->getEntity())) {
            $this->entityMultimediaPersistedRoutine($object, $em);
        }
        if ($object instanceof Image || $object instanceof User || $object instanceof Page) {
            $this->imagePersistedOrUpdatedRoutine($object);
        }
    }

    public function postUpdate(LifecycleEventArgs $args)
    {      
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof EntityUser) {
            $this->entityUserUpdatedRoutine($object, $em);
        }
        if ($object instanceof PageUser) {
            $this->pageUserUpdatedRoutine($object, $em);
        }
        if ($object instanceof EntityTranslation && $object->getEntity() instanceof Biography) {   
            $this->biographyUpdatedRoutine($object->getEntity(), $em);
        }
        if ($object instanceof Fan) {
            $this->fanUpdatedRoutine($object, $em);
        }
        if ($object instanceof ImageAlbum) {
            $this->imageAlbumUpdatedRoutine($object, $em);
        }
        if (($object instanceof User || $object instanceof Page)
            && (array_key_exists('img', $em->getUnitOfWork()->getEntityChangeSet($object)) 
                || array_key_exists('cover_img', $em->getUnitOfWork()->getEntityChangeSet($object)) 
                || array_key_exists('background_img', $em->getUnitOfWork()->getEntityChangeSet($object)))) {
            $this->imgUpdatedRoutine($object, $em);
        }
        if ($object instanceof Relation && $object->getAccepted() == Relation::ACCEPTED_BOTH) {
            $this->relationUpdatedRoutine($object, $em);
        }
        if ($object instanceof User && !is_null($this->get('security.context')->getToken())) {
            $this->get('cm.user_authentication')->updateProfile();
        }
        if ($object instanceof Image || $object instanceof User || $object instanceof Page) {
            $this->imagePersistedOrUpdatedRoutine($object);
        }
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $object = $args->getEntity();
        $em = $args->getEntityManager();

        if ($object instanceof Page) {
            $this->pagePersistedRoutine($object, $em);
        }
        if ($object instanceof EntityUser) {
            $this->entityUserRemovedRoutine($object, $em);
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
        if ($object instanceof User || $object instanceof Page) {
            $this->imgRemovedRoutine($object, $em);
        }
        if ($object instanceof Relation) {
            $this->relationRemovedRoutine($object, $em);
        }
        if ($object instanceof Multimedia) {
            $this->entityMultimediaRemovedRoutine($object, $em);
        }
        if ($object instanceof Image || $object instanceof User || $object instanceof Page) {
            $this->imageRemovedRoutine($object);
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

    // private function userPersistedRoutine(User &$user, EntityManager $em)
    // {
    //     $post = $this->get('cm.post_center')->newPost(
    //         is_null($this->getUser()) ? $user : $this->getUser(),
    //         $user,
    //         Post::TYPE_CREATION,
    //         $user->className()
    //     );

    //     $this->container->get('logger')->info('CMBundle_info '.$user.' '.$post->getType());// ##########################################################################

    //     $em->persist($post);

    //     $flushNeeded = true;
    // }

    private function entityUserPersistedRoutine(EntityUser &$entityUser, EntityManager $em)
    {
        $entity = $entityUser->getEntity();
        $post = $entity->getPost();
        $user = $post->getUser();
        $page = $post->getPage();

        $requestCenter = $this->get('cm.request_center');

        switch ($entityUser->getStatus()) {
            case EntityUser::STATUS_PENDING:
                $requestCenter->newRequest(
                    $entityUser->getUser(),
                    $user,
                    $entityUser->className(),
                    $entityUser->getId(),
                    $entity,
                    $page
                );
                break;
            case EntityUser::STATUS_REQUESTED:
                foreach ($em->getRepository('CMBundle:Entity')->getAdmins($entity->getId()) as $admin) {
                    $requestCenter->newRequest(
                        $admin,
                        $entityUser->getUser(),
                        $entityUser->className(),
                        $entityUser->getId(),
                        $entity,
                        $page
                    );
                }
                break;
        }
    }

    private function entityUserUpdatedRoutine(EntityUser &$entityUser, EntityManager $em)
    {
        if ($entityUser->getStatus() == EntityUser::STATUS_ACTIVE) {
            $entity = $entityUser->getEntity();
            $post = $entity->getPost();
            $page = $post->getPage();
            
            if ($entityUser->getUserId() == $this->getUser()->getId()) {
                $toNotify = $em->getRepository('CMBundle:Entity')->getAdmins($entity->getId());
                $type = Notification::TYPE_REQUEST_ACCEPTED_BY_USER;
            } else {
                $toNotify = array($entityUser->getUser());
                $type = Notification::TYPE_REQUEST_ACCEPTED_BY_ADMIN;
            }

            foreach ($toNotify as $toUser) {
                $this->get('cm.notification_center')->newNotification(
                    $type,
                    $toUser,
                    $this->getUser(),
                    null,
                    null,
                    $post,
                    $page
                );
            }
        }

        $this->get('cm.request_center')->removeRequests(null, array('object' => $entityUser->className(), 'objectId' => $entityUser->getId()));
    }

    private function entityUserRemovedRoutine(EntityUser &$entityUser, EntityManager $em)
    {
        $this->get('cm.request_center')->removeRequests(null, array('object' => $entityUser->className(), 'objectId' => $entityUser->getId()));
    }

    private function pageUserPersistedRoutine(PageUser &$pageUser, EntityManager $em)
    {
        $page = $pageUser->getPage();
        $post = $page->getPost();
        $user = $post->getUser();

        $requestCenter = $this->get('cm.request_center');

        switch ($pageUser->getStatus()) {
            case PageUser::STATUS_PENDING:
                $requestCenter->newRequest(
                    $pageUser->getUser(),
                    $user,
                    $pageUser->className(),
                    $pageUser->getId(),
                    null,
                    $page
                );
                break;
            case PageUser::STATUS_REQUESTED:
                foreach ($em->getRepository('CMBundle:Page')->getAdmins($page->getId()) as $admin) {
                    $requestCenter->newRequest(
                        $admin,
                        $pageUser->getUser(),
                        $pageUser->className(),
                        $pageUser->getId(),
                        null,
                        $page
                    );
                }
                break;
        }
    }

    private function pageUserUpdatedRoutine(PageUser &$pageUser, EntityManager $em)
    {
        if ($pageUser->getStatus() == PageUser::STATUS_ACTIVE) {
            $page = $pageUser->getPage();
            $post = $page->getPost();

            if ($pageUser->getUserId() == $this->getUser()->getId()) {
                $toNotify = $em->getRepository('CMBundle:Page')->getAdmins($page->getId());;
                $type = Notification::TYPE_REQUEST_ACCEPTED_BY_USER;
            } else {
                $toNotify = array($pageUser->getUser());
                $type = Notification::TYPE_REQUEST_ACCEPTED_BY_ADMIN;
            }

            foreach ($toNotify as $toUser) {
                $this->get('cm.notification_center')->newNotification(
                    $type,
                    $toUser,
                    $this->getUser(),
                    null,
                    nulll,
                    $post,
                    $page
                );
            }
        }

        $this->get('cm.request_center')->removeRequests(null, array('object' => $pageUser->className(), 'objectId' => $pageUser->getId()));
    }

    private function pageUserRemovedRoutine(PageUser &$pageUser, EntityManager $em)
    {
        $this->get('cm.request_center')->removeRequests(null, array('object' => $pageUser->className(), 'objectId' => $pageUser->getId()));
    }
    
    private function postAggregatePersistedRoutine(Post &$post, EntityManager $em)
    {
        switch ($post->getObject()) {
            case Comment::className():
            case Like::className():
                $type = $em->getRepository('CMBundle:'.$this->container->get('cm.helper')->className($post->getObject()))->findOneById($post->getObjectIds()[0])->getImageId();
                break;
        }
        $arrayPost = $em->getRepository('CMBundle:Post')->getLastPosts(array(
            'entityId' => $post->getEntityId(),
            'object' => $post->getObject().'['.$type.']',
            'aggregate' => true,
            'after' => new \DateTime('-1 week'),
            'paginate' => false,
            'limit' => 1
        ));

        if (count($arrayPost) >= 1) {
            $arrayPost = $arrayPost[0];
            $arrayPost->setUser($post->getUser());

            $arrayPost->addObjectId($post->getObjectIds()[0]);
            $old = 'old';
        } else {
            $arrayPost = $this->get('cm.post_center')->newPost(
                $post->getCreator(),
                $post->getUser(),
                Post::TYPE_AGGREGATE,
                $post->getObject().'['.$type.']',
                array($post->getObjectIds()[0]),
                $post->getEntity()
            );
            $old = 'new';
        }

        $em->persist($arrayPost);

        $this->flushNeeded = true;
    }

    private function commentPersistedRoutine(Comment &$comment, EntityManager $em)
    {
        if (!is_null($comment->getPost())) {
            $post = $comment->getPost();
            $entity = $post->getEntity();
            $object = $post;
            $objectType = $entity->className();
            $toUser = $post->getUser();
            $toCreator = $post->getCreator();
        } else {
            $post = null;
            $entity = null;
            $object = $comment->getImage();
            $objectType = $object->className();
            $toUser = $object->getUser();
            $toCreator = $object->getUser();
        }

        $this->get('cm.post_center')->newPost(
            $comment->getUser(),
            $comment->getUser(),
            Post::TYPE_CREATION,
            $comment->className(),
            array($comment->getId()),
            $entity
        );

        $notifiedUserIds = array($comment->getUser()->getId());

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_COMMENT,
            $toUser,
            $comment->getUser(),
            $objectType,
            $comment->getId(),
            $post,
            $object->getPage()
        );
        $notifiedUserIds[] = $toUser->getId();

        if ($toCreator->getId() != $toUser->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_COMMENT,
                $toCreator,
                $comment->getUser(),
                $objectType,
                $comment->getId(),
                $post,
                $object->getPage()
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
                $objectType,
                $comment->getId(),
                $comment->getPost(),
                $object->getPage()
            );
        }
    }

    private function commentRemovedRoutine(Comment &$comment, EntityManager $em)
    {
        $post = $em->getRepository('CMBundle:Post')->getLastPosts(array(
            'object' => get_class($comment),
            'objectId' => $comment->getId(),
            'paginate' => false,
            'limit' => 1
        ));

        if (count($post) >= 1) {
            $post[0]->removeObjectId($like->getId());

            if (count($post[0]->getObjectIds()) == 0) {
                $em->remove($post[0]);
            }
        }

        $this->get('cm.notification_center')->removeNotifications($comment->getUser()->getId(), null, $comment->getId(), Notification::TYPE_COMMENT);
    }

    private function likePersistedRoutine(Like &$like, EntityManager $em)
    {
        if (!is_null($like->getPost())) {
            $post = $like->getPost();
            $entity = $post->getEntity();
            $object = $post;
            $objectType = $entity->className();
            $toUser = $post->getUser();
            $toCreator = $post->getCreator();
        } else {
            $post = null;
            $entity = null;
            $object = $like->getImage();
            $objectType = $object->className();
            $toUser = $object->getUser();
            $toCreator = $object->getUser();
        }

            $this->get('cm.post_center')->newPost(
                $like->getUser(),
                $like->getUser(),
                Post::TYPE_CREATION,
                $like->className(),
                array($like->getId()),
                $entity
            );

        $this->get('cm.notification_center')->newNotification(
            Notification::TYPE_LIKE,
            $toUser,
            $like->getUser(),
            $objectType,
            $like->getId(),
            $post,
            $object->getPage()
        );

        if ($toCreator->getId() != $toUser->getId()) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_LIKE,
                $toCreator,
                $like->getUser(),
                $objectType,
                $like->getId(),
                $post,
                $object->getPage()
            );
        }
    }

    private function likeRemovedRoutine(Like &$like, EntityManager $em)
    {
        $post = $em->getRepository('CMBundle:Post')->getLastPosts(array(
            'like' => true,
            'object' => get_class($like),
            'objectId' => $like->getId(),
            'paginate' => false,
            'limit' => 1
        ));

        if (count($post) >= 1) {
            $post[0]->removeObjectId($like->getId());

            if (count($post[0]->getObjectIds()) == 0) {
                $em->remove($post[0]);
            }
        }

        $this->get('cm.notification_center')->removeNotifications($like->getUser()->getId(), null, $like->getId(), Notification::TYPE_LIKE);
    }

    private function biographyUpdatedRoutine(Biography &$biography, EntityManager $em)
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

    private function biographyRemovedRoutine(Biography &$biography, EntityManager $em)
    {
        $this->get('cm.post_center')->removePost(
            $this->getUser(),
            $this->getUser(),
            get_class($biography),
            array($biography->getId())
        );
    }

    private function fanPersistedRoutine(Fan &$fan, EntityManager $em)
    {
        if (!is_null($fan->getUser())) {
            $postType = Post::TYPE_FAN_USER;
        } elseif (!is_null($fan->getPage())) {
            $postType = Post::TYPE_FAN_PAGE;
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
                $fan->className(),
                array($fan->getId())
            );
        }

        $toNotify = array();
        if (!is_null($fan->getUser())) {
            $toNotify[] = $fan->getUser();
        } elseif (!is_null($fan->getPage())) {
            $toNotify = $em->getRepository('CMBundle:Page')->getAdmins($fan->getPage()->getId());
        }

        foreach ($toNotify as $user) {
            $this->get('cm.notification_center')->newNotification(
                Notification::TYPE_FAN,
                $user,
                $fan->getFromUser(),
                !is_null($fan->getUser()) ? $fan->getUser()->className() : $fan->getPage()->className(),
                $fan->getId(),
                $post,
                $fan->getPage()
            );
        }
    }

    private function fanRemovedRoutine(Fan &$fan, EntityManager $em)
    {
        if (!is_null($fan->getUser())) {
            $postType = Post::TYPE_FAN_USER;
        } elseif (!is_null($fan->getPage())) {
            $postType = Post::TYPE_FAN_PAGE;
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

        $this->get('cm.notification_center')->removeNotifications($fan->getFromUser()->getId(), null, $fan->getId(), Notification::TYPE_FAN);
    }

    private function imagePersistedRoutine(Image &$image, EntityManager $em)
    {
        $entity = $image->getEntity();
        $user = $image->getUser();
        $page = $image->getPage();

        if ((!is_null($entity) && is_null($entity->getId())) || $image->getMain()) {
            return;
        }

        $post = $em->getRepository('CMBundle:Post')->getLastPosts(array(
            'entityId' => $entity->getId(),
            'object' => get_class($image),
            'userId' => is_null($user) ? null : $user->getId(),
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
                ->setObject(get_class($image))
                ->addObjectId($image->getId());

            $entity->addPost($post, false);
        }

        $this->flushNeeded = true;
    }

    public function imageAlbumUpdatedRoutine(ImageAlbum &$album, EntityManager $em)
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

            $album->addPost($post);
        }

        $em->persist($album);

        $this->flushNeeded = true;
    }

    private function imgPersistedRoutine(&$publisher, EntityManager $em)
    {
        $uow = $em->getUnitOfWork();

        if ($publisher instanceof User) {
            $user = $publisher;
            $page = null;
        }
        if ($publisher instanceof Page) {
            $page = $publisher;
            $user = is_null($this->get('security.context')->getToken()) ? $page->getCreator() : $this->getUser();
        }

        $images = array();
        if (!is_null($publisher->getImg())) {
            $images[ImageAlbum::TYPE_PROFILE] = $publisher->getImg();
        }
        if (!is_null($publisher->getCoverImg())) {
            $images[ImageAlbum::TYPE_COVER] = $publisher->getCoverImg();
        }
        if (property_exists($publisher, 'backgroundImg') && !is_null($publisher->getBackgroundImg())) {
            $images[ImageAlbum::TYPE_BACKGROUND] = $publisher->getBackgroundImg();
        }

        foreach ($images as $type => $img) {
        $this->container->get('logger')->info('CMBundle_info '.$type.' '.$img); // ##########################################################################
            $album = new ImageAlbum;
            $image = new Image;

            $album->setType($type);
            
            $image->setImg($img);

            $post = $this->get('cm.post_center')->getNewPost($user, $user);
            $post->setPage($page);
            $album->setPost($post);

            $image->setMain(true)
                ->setUser($user)
                ->setPage($page);
            $album->setImage($image);

            $em->persist($album);
            $em->persist($image);
            $em->persist($post);
            $uow->computeChangeSet($em->getClassmetadata($image->className()), $image);
            $uow->computeChangeSet($em->getClassmetadata($album->className()), $album);
            $uow->computeChangeSet($em->getClassmetadata($post->className()), $post);
        }

        $this->flushNeeded = true;
    }

    private function imgUpdatedRoutine(&$publisher, EntityManager $em)
    {
        $uow = $em->getUnitOfWork();
     
        $user = null;
        if ($publisher instanceof User) {
            $user = $publisher;
        }
        $page = null;
        if ($publisher instanceof Page) {
            $page = $publisher;
        }

        $images = array();
        if (array_key_exists('img', $em->getUnitOfWork()->getEntityChangeSet($publisher))) {
            $images[ImageAlbum::TYPE_PROFILE] = array('img' => $publisher->getImg(), 'offset' => $publisher->getImgOffset());
        }
        if (array_key_exists('cover_img', $em->getUnitOfWork()->getEntityChangeSet($publisher))) {
            $images[ImageAlbum::TYPE_COVER] = array('img' => $publisher->getCoverImg(), 'offset' => $publisher->getCoverImgOffset());
        }
        if (array_key_exists('background_img', $em->getUnitOfWork()->getEntityChangeSet($publisher))) {
            $images[ImageAlbum::TYPE_BACKGROUND] = array('img' => $publisher->getBackgroundImg(), 'offset' => null);
        }

        foreach ($images as $type => $img) {
            $album = $em->getRepository('CMBundle:ImageAlbum')->getImageAlbum(array(
                'userId' => is_null($user) ? null : $user->getId(),
                'pageId' => is_null($page) ? null : $page->getId(),
                'type' => $type,
            ));
            if (is_null($album)) {
                $album = new ImageAlbum;
                $album->setType($type);

                $post = $this->get('cm.post_center')->getNewPost($user, $user);
                $post->setPage($page);
                $album->setPost($post);
            }

            $image = new Image;
            $image->setImg($img['img'])
                ->setImgOffset($img['offset'])
                ->setMain(true)
                ->setUser($user)
                ->setPage($page);
            $album->addImage($image);

            $em->persist($album);
            $em->persist($image);
            $uow->computeChangeSet($em->getClassmetadata($image->className()), $image);
            $uow->computeChangeSet($em->getClassmetadata($album->className()), $album);
            if (!is_null($post)) {
                $em->persist($post);
                $uow->computeChangeSet($em->getClassmetadata($post->className()), $post);
            }
        }
    }

    private function imgRemovedRoutine(&$publisher, EntityManager $em)
    {
        // $this->get('cm.post_center')->removePost(
        //     $this->getUser(),
        //     $this->getUser(),
        //     get_class($biography),
        //     array($biography->getId())
        // );
    }

    private function relationOutPersistedRoutine(Relation &$relation, EntityManager $em)
    {
        $inverse = new Relation;
        $inverse->setUser($relation->getFromUser())
            ->setFromUser($relation->getUser())
            ->setAccepted(Relation::ACCEPTED_NO);

        $relation->getRelationType()->getInverseType()->addRelation($inverse);

        $em->persist($inverse);

        $this->flushNeeded = true;
    }

    private function relationInPersistedRoutine(Relation &$relation, EntityManager $em)
    {
        $this->get('cm.request_center')->newRequest(
            $relation->getFromUser(),
            $relation->getUser(),
            get_class($relation),
            $relation->getId()
        );
    }

    private function relationUpdatedRoutine(Relation &$relation, EntityManager $em)
    {
        $post = $this->get('cm.post_center')->newPost(
            $relation->getUser(),
            $relation->getUser(),
            Post::TYPE_CREATION,
            get_class($relation),
            array($relation->getId())
        );

        $em->getRepository('CMBundle:Request')->delete($this->getUser()->getId(), array(
            'object' => $relation->className(),
            'objectId' => $relation->getId()
        ));

        $em->getRepository('CMBundle:Relation')->update($relation->getRelationType()->getInverseTypeId(), $relation->getUserId(), $relation->getFromUserId(), Relation::ACCEPTED_BOTH);

        $inverse = $em->getRepository('CMBundle:Relation')->findOneBy(array(
            'relationTypeId' => $relation->getRelationType()->getInverseTypeId(),
            'fromUserId' => $relation->getUserId(),
            'userId' => $relation->getFromUserId()
        ));

        $post = $this->get('cm.post_center')->newPost(
            $inverse->getFromUser(),
            $inverse->getUser(),
            Post::TYPE_CREATION,
            get_class($inverse),
            array($inverse->getId())
        );
    }

    private function relationRemovedRoutine(Relation &$relation, EntityManager $em)
    {
        $em->getRepository('CMBundle:Request')->delete($relation->getFromUserId(), array(
            'object' => $relation->className(),
            'objectId' => $relation->getId()
        ));

        $this->get('cm.post_center')->removePost(
            $relation->getUser(),
            $relation->getUser(),
            get_class($relation),
            array($relation->getId())
        );

        $inverse = $em->getRepository('CMBundle:Relation')->findOneBy(array(
            'relationTypeId' => $relation->getRelationType()->getInverseTypeId(),
            'fromUserId' => $relation->getUserId(),
            'userId' => $relation->getFromUserId()
        ));

        $em->getRepository('CMBundle:Relation')->remove($inverse->getRelationTypeId(), $inverse->getUserId(), $inverse->getFromUserId());

        $em->getRepository('CMBundle:Request')->delete($inverse->getFromUserId(), array(
            'object' => $inverse->className(),
            'objectId' => $inverse->getId()
        ));
        
        $this->get('cm.post_center')->removePost(
            $inverse->getUser(),
            $inverse->getUser(),
            get_class($inverse),
            array($inverse->getId())
        );
    }

    private function entityMultimediaPersistedRoutine(Multimedia &$multimedia, EntityManager $em)
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

    private function entityMultimediaRemovedRoutine(Multimedia &$multimedia, EntityManager $em)
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

    private function imagePersistingOrUpdatingRoutine(&$image, LifecycleEventArgs $args = null)
    {
        if (method_exists($image, 'getImgFile') && !is_null($image->getImgFile())) {
            if (!is_null($image->getOldImg())) {
                $this->imageRemovedRoutine($image, $image->getOldImg());
            }
            $fileName = md5(uniqid().$image->getImgFile()->getClientOriginalName().time()).'.'.$image->getImgFile()->guessExtension();
            $image->setImg($fileName);
            if (!is_null($args)) {
                $args->setNewValue('img', $fileName);
            }
        }
        if (method_exists($image, 'getCoverImgFile') && !is_null($image->getCoverImgFile())) {
            if (!is_null($image->getOldCoverImg())) {
                $this->imageRemovedRoutine($image, $image->getOldCoverImg());
            }
            $fileName = md5(uniqid().$image->getCoverImgFile()->getClientOriginalName().time()).'.'.$image->getImgFile()->guessExtension();
            $image->setCoverImg($fileName);
            if (!is_null($args)) {
                $args->setNewValue('coverImg', $fileName);
            }
        }
        if (method_exists($image, 'getBackgroundImgFile') && !is_null($image->getBackgroundImgFile())) {
            if (!is_null($image->getOldBackgroundImg())) {
                $this->imageRemovedRoutine($image, $image->getOldBackgroundImg());
            }
            $fileName = md5(uniqid().$image->getBackgroundImgFile()->getClientOriginalName().time()).'.'.$image->getImgFile()->guessExtension();
            $image->setBackgroundImg($fileName);
            if (!is_null($args)) {
                $args->setNewValue('backgroundImg', $fileName);
            }
        }
    }

    private function imagePersistedOrUpdatedRoutine(&$image)
    {
        $dir = getcwd().$this->container->getParameter('images.dir').'/full';

        if (method_exists($image, 'getImgFile') && !is_null($image->getImgFile())) {
            $image->getImgFile()->move($dir, $image->getImg());
            $this->container->get('logger')->info($image->getImg());
        }
        if (method_exists($image, 'getCoverImgFile') && !is_null($image->getCoverImgFile())) {
            $image->getImgFile()->move($dir, $image->getCoverImg());
        }
        if (method_exists($image, 'getBackgroundImgFile') && !is_null($image->getBackgroundImgFile())) {
            $image->getImgFile()->move($dir, $image->getBackgroundImg());
        }
    }

    private function imageRemovedRoutine(&$image, $old = null)
    {
        $dir = getcwd().$this->container->getParameter('images.dir');
        $folders = array_keys($this->container->getParameter('liip_imagine.filter_sets'));

        $fileNames = array();
        if (is_null($old)) {
            if (method_exists($image, 'getImg') && !is_null($image->getImg())) {
                $fileNames[] = $image->getImg();
            }
            if (method_exists($image, 'getCoverImg') && !is_null($image->getCoverImg())) {
                $fileNames[] = $image->getCoverImg();
            }
            if (method_exists($image, 'getBackgroundImg') && !is_null($image->getBackgroundImg())) {
                $fileNames[] = $image->getBackgroundImg();
            }
        } else {
            $fileNames[] = $old;
        }

        foreach ($fileNames as $fileName) {
            foreach ($folders as $folder) {
                $file = $dir.'/'.$folder.'/'.$fileName;

                if (file_exists($file)) {
                    unlink($file);
                }
            }
        }
    }
}