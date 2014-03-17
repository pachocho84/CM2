<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Image;

class ImagesFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < Entities\EventFixtures::count(); $i++) {
            $event = $manager->merge($this->getReference('event-'.$i));
            $user = $event->getPost()->getUser();

            for ($j = rand(1, 4); $j > 0; $j--) {
                $image = new Image;
                $image->setImg($event->getImage()->getImg())
                    ->setText('image number '.$j.' for event \"'.$event->getTitle().'\"')
                    ->setMain(false)
                    ->setUser($user);
                
                $event->addImage($image);
             
                $manager->flush();
            }
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 80;
    }
}