<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Fan;

class FanFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 9; $i++) {
            $ofUser = $manager->merge($this->getReference('user-'.$i));

            $numbers = range(1, UserFixtures::count());
            unset($numbers[$i - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 7); $j++) {
                $user = $manager->merge($this->getReference('user-'.$numbers[$j]));

                $fan = new Fan;
                $user->addFanOf($fan);
                $ofUser->addFan($fan);
                $manager->persist($fan);
            }
        }

        for ($i = 1; $i < 21; $i++) {
            $ofGroup = $manager->merge($this->getReference('group-'.$i));

            $numbers = range(1, UserFixtures::count());
            unset($numbers[$i - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 7); $j++) {
                $user = $manager->merge($this->getReference('user-'.$numbers[$j]));

                $fan = new Fan;
                $user->addFanOf($fan);
                $ofGroup->addFan($fan);
                $manager->persist($fan);
            }
        }

        for ($i = 1; $i < 4; $i++) {
            $ofPage = $manager->merge($this->getReference('page-'.$i));

            $numbers = range(1, UserFixtures::count());
            unset($numbers[$i - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 7); $j++) {
                $user = $manager->merge($this->getReference('user-'.$numbers[$j]));

                $fan = new Fan;
                $user->addFanOf($fan);
                $ofPage->addFan($fan);
                $manager->persist($fan);
            }
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 90;
    }
}