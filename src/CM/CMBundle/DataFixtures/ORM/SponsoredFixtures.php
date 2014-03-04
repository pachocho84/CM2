<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Sponsored;

class SponsoredFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 21; $i++) {
        
            $sponsored = new Sponsored;
            $event = $manager->merge($this->getReference('event-'.rand(1, 10)));
            $sponsored->setEntity($event)
                ->setUser($event->getPost()->getUser())
                ->setViews(rand(0, 100));
            $dateStart = new \DateTime;
            $dateStart->setTimestamp(time() - 604800);
            $sponsored->setStart($dateStart);
            $dateEnd = new \DateTime;
            $dateEnd->setTimestamp(time() + 604800);
            $sponsored->setEnd($dateEnd);
            
            $manager->persist($sponsored);
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 80;
    }
}