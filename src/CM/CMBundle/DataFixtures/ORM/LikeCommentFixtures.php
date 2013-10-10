<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Like;
use CM\UserBundle\Entity\User;

class LikeCommentFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 201; $i++) {

            for ($j = 1; $j < rand(1, 10); $j++) {
                $like = new Like;
                $user = $manager->merge($this->getReference('user-'.rand(1, 10)));
                $like->setUser($user);
                $post = $manager->merge($this->getReference('event-'.$i))->getPosts()[0];
                $like->setPost($post);

                $manager->persist($like);
            }
                
            if ($i % 10 == 0) {
                echo $i." - ";
                $manager->flush();
            }
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 4;
    }
}