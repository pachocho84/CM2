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
        1 => array( /* Ernesto Casareto */
            array('user' => 2, 'type' => 4), /* Fabrizio Castellarin - Colleagues */
            array('user' => 2, 'type' => 5), /* Fabrizio Castellarin - Friends */
            array('user' => 3, 'type' => 4), /* Federica Fontana - Colleagues */
            array('user' => 3, 'type' => 6), /* Federica Fontana - Family */
            array('user' => 4, 'type' => 6), /* Luca Casareto - Family */
            array('user' => 6, 'type' => 4), /* Mario Marcarini - Colleagues */
            array('user' => 7, 'type' => 5), /* Dora Alberti - Colleagues */
            array('user' => 8, 'type' => 5), /* Francesca Cremonini - Colleagues */
            array('user' => 9, 'type' => 5), /* Luca Di Giulio - Colleagues */
        ),
        2 => array( /* Fabrizio Castellarin */
            array('user' => 3, 'type' => 4), /* Federica Fontana - Colleagues */
            array('user' => 5, 'type' => 5), /* Virginia Nolte - Friends */
            array('user' => 4, 'type' => 5), /* Luca Casareto - Friends */
        ),
        3 => array( /* Federica Fontana */
            array('user' => 4, 'type' => 5), /* Luca Casareto - Family */
            array('user' => 7, 'type' => 5), /* Dora Alberti - Colleagues */
            array('user' => 8, 'type' => 5), /* Francesca Cremonini - Colleagues */
            array('user' => 9, 'type' => 5), /* Luca Di Giulio - Colleagues */
        ),
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
        
        foreach ($this->relations as $user => $relArray) {
            foreach ($relArray as $rel) {
                $relation = new Relation;
                $relation->setUser($manager->merge($this->getReference('user-'.$user)))
                    ->setFromUser($manager->merge($this->getReference('user-'.$rel['user'])))
                    ->setAccepted(Relation::ACCEPTED_BOTH);
        
                $relationType = $manager->merge($this->getReference('relationType-'.$rel['type']));
                $relationType->addRelation($relation);
        
                $manager->persist($relation);
                
                $inverse = new Relation;
                $inverse->setUser($manager->merge($this->getReference('user-'.$rel['user'])))
                    ->setFromUser($manager->merge($this->getReference('user-'.$user)))
                    ->setAccepted(Relation::ACCEPTED_BOTH);
        
                $relationType->getInverseType()->addRelation($inverse);
                
                $manager->persist($inverse);
            }
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 130;
    }
}