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