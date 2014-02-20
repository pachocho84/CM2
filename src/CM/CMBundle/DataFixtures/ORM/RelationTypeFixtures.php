<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\RelationType;

class RelationTypeFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private $relationTypes = array(
    	array('name' => 'student', 'inverse' => 1),
    	array('name' => 'teacher', 'inverse' => 0),
    	array('name' => 'class mate', 'inverse' => 2),
    	array('name' => 'colleagues', 'inverse' => 3),
    	array('name' => 'friend', 'inverse' => 4),
    	array('name' => 'family', 'inverse' => 5),
    	array('name' => 'aquitance', 'inverse' => 6),
    	array('name' => 'following', 'inverse' => 7),
    );

    public function load(ObjectManager $manager)
    {
        $rels = array();
        foreach ($this->relationTypes as $i => $rel) {
            $relationType = new RelationType;
            $relationType->setName($rel['name']);

            $manager->persist($relationType);

            $rels[] = $relationType;
        }

        foreach ($this->relationTypes as $i => $rel) {
            $rels[$i]->setInverseType($rels[$rel['inverse']]);
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 130;
    }
}