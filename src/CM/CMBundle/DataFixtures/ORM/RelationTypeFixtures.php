<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\RelationType;
use CM\CMBundle\Entity\Relation;

class RelationTypeFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private $relationTypes = array(
    	/* 1 */ array('name' => 'Students', 'inverse' => 1),
    	/* 2 */ array('name' => 'Teachers', 'inverse' => 0),
    	/* 3 */ array('name' => 'Class mates', 'inverse' => 2),
    	/* 4 */ array('name' => 'Colleagues', 'inverse' => 3),
    	/* 5 */ array('name' => 'Friends', 'inverse' => 4),
    	/* 6 */ array('name' => 'Family', 'inverse' => 5),
    	/* 7 */ array('name' => 'Aquitances', 'inverse' => 6),
    	/* 8 */ array('name' => 'Following', 'inverse' => 7),
    );
    
    private $relations = array(
        1 => array(
            array('user' => 2, 'type' => 4),
            array('user' => 3, 'type' => 4)
        )
    );

    public function load(ObjectManager $manager)
    {
        $rels = array();
        foreach ($this->relationTypes as $i => $rel) {
            $relationType = new RelationType;
            $relationType->setName($rel['name']);

            $manager->persist($relationType);
            $this->addReference('relationType-'.($i + 1), $relationType);

            $rels[] = $relationType;
        }

        foreach ($this->relationTypes as $i => $rel) {
            $rels[$i]->setInverseType($rels[$rel['inverse']]);
        }
    
        $manager->flush();
        
        foreach ($this->relations as $user => $relation) {
            $relation = new Relation;
            $relation->setUser($manager->merge($this->getReference('user-'.$user)))
                ->setFromUser($manager->merge($this->getReference('user-'.$relation['user'])))
                ->setAccepted(Relation::ACCEPTED_BOTH);
    
            $manager->merge($this->getReference('relationType-'.$relation['type']))->addRelation($relation);
    
            $em->persist($relation);
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 130;
    }
}