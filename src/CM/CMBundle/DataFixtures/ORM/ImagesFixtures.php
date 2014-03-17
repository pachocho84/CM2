<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Image;

class ImagesFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private $relationTypes = array(
        array('name' => 'Students', 'inverse' => 1),
        array('name' => 'Teachers', 'inverse' => 0),
        array('name' => 'Class mates', 'inverse' => 2),
        array('name' => 'Colleagues', 'inverse' => 3),
        array('name' => 'Friends', 'inverse' => 4),
        array('name' => 'Family', 'inverse' => 5),
        array('name' => 'Aquitances', 'inverse' => 6),
        array('name' => 'Following', 'inverse' => 7),
    );

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