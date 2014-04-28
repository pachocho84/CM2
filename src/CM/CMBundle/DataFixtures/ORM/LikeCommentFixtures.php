<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\Comment;
use CM\CMBundle\Entity\User;
use CM\CMBundle\DataFixtures\ORM\Entities;

class LikeCommentFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < Entities\EventFixtures::count(); $i++) {
            $post = $manager->merge($this->getReference('event-'.$i))->getPost();

            for ($j = 1; $j < rand(1, 10); $j++) {
                $like = new Like;
                $user = $manager->merge($this->getReference('user-'.rand(1, UserFixtures::count())));
                $like->setUser($user);
                $post->addLike($like);

                var_dump($user.' p:'.$post->getId());
            }

            for ($j = 1; $j < rand(1, 11); $j++) {
                $comment = new Comment;
                $comment->setComment("Comment");
                $user = $manager->merge($this->getReference('user-'.rand(1, UserFixtures::count())));
                $comment->setUser($user);
                $post->addComment($comment);
            }

            $manager->persist($post);
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 70;
    }
}