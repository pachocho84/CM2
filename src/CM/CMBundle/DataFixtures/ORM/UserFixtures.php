<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityCategory;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $people = array(
        array('firstname' => 'Ernesto',
            'lastname' => 'Casareto',
            'username' => 'pachocho',
            'email' => 'ernesto@circuitomusica.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Padua',
            'city_current' => 'Milan',
            'birth_date' => array(1984, 6, 17),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'image' => '1074169_10201332211257910_2077101884_o.jpg',
            'user_tags' => array(1, 5)
        ),
        array('firstname' => 'Fabrizio', 
            'lastname' => 'Castellarin',
            'username' => 'EnoahNetzach',
            'email' => 'f.castellarin@gmail.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1990, 4, 19),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'image' => '00b7b971d96ce05797e6757e5a0a4232.jpeg',
            'user_tags' => array(4)
        ),
        array('firstname' => 'Federica',
            'lastname' => 'Fontana',
            'username' => 'fede_pucci_pucci',
            'email' => 'f.fontana@gmail.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Taranto',
            'city_current' => 'Milan',
            'birth_date' => array(1900, 4, 15),
            'birth_date_visible' => User::BIRTHDATE_NO_YEAR,
            'image' => '664508_4284090172825_1093880282_o.jpg',
            'user_tags' => array(1)
        ),
        array('firstname' => 'Luca',
            'lastname' => 'Casareto',
            'username' => 'ghey',
            'email' => 'lucasareto@gmail.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Padua',
            'city_current' => 'Milan',
            'birth_date' => array(1990, 5, 15),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'image' => '263757_2174482600735_3488733_n.jpg',
            'user_tags' => array()
        ),
        array('firstname' => 'Virginia Alexandra',
            'lastname' => 'Nolte',
            'username' => 'cinnamoon',
            'email' => 'vir@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1994, 3, 29),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'image' => '1394182_652549711456783_1108378362_n.jpg',
            'user_tags' => array()
        ),
        array('firstname' => 'Mario',
            'lastname' => 'Marcarini',
            'username' => 'mariomarcarini',
            'email' => 'mamarcarini@circuitomusica.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1971, 3, 21),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'image' => '8d89c5d232b971a8fbad65abe5fa21b0de7e1048.jpg',
            'user_tags' => array(9, 8, 10)
        ),
        array('firstname' => 'Dora',
            'lastname' => 'Alberti',
            'username' => 'doralberti',
            'email' => 'doralberti@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1971, 3, 12),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'image' => 'e9ad7f19c9c4909fb82ff647d5fb43527e4f1aad.jpg',
            'user_tags' => array()
        ),
        array('firstname' => 'Francesca',
            'lastname' => 'Cremonini',
            'username' => 'fracremonini',
            'email' => 'fracremonini@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1964, 11, 17),
            'birth_date_visible' => User::BIRTHDATE_NO_YEAR,
            'image' => 'b0702dfa74da4333342e764750476cfddd4c5b12.jpg',
            'user_tags' => array()
        ),
    );
	
	/**
	 * {@inheritDoc}
	 */
	public function setContainer(ContainerInterface $container = null)
	{
	    $this->container = $container;
	}

	public function load(ObjectManager $manager)
	{
		$userManager = $this->container->get('fos_user.user_manager');

        foreach ($this->people as $i => $person) {
			$user = $userManager->createUser();
			$date = new \DateTime;
			$user->setEmail($person['email'])
		  	   ->setUsername($person['username'])
		  	   ->setPlainPassword('pacho')
		  	   ->setEnabled(true)
		  	   ->setFirstName($person['firstname'])
		  	   ->setLastName($person['lastname'])
		  	   ->setSex($person['sex'])
		  	   ->setCityBirth($person['city_birth'])
		  	   ->setCityCurrent($person['city_current'])
		  	   ->setBirthDate($date->setDate($person['birth_date'][0], $person['birth_date'][1], $person['birth_date'][2]))
		  	   ->setBirthDateVisible($person['birth_date_visible'])
		  	   ->setImg($person['image']);
            if (!is_null($person['cover'])) {
                $user->setCoverImg($person['cover']);
            }

			$manager->persist($user);

            for ($j = 0; $j < count($person['user_tags']); $j++) {
                $userTag = $manager->merge($this->getReference('user_tag-'.$person['user_tags'][$j]));
                
                $user->addUserTag($userTag, $j);
            }

			$this->addReference('user-'.($i + 1), $user);
		}
        
        $manager->flush();
	}

	public function getOrder()
	{
        return 20;
    }
}