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
            'image' => '00b7b971d96ce05797e6757e5a0a4232.jpeg'
        ),
        array('firstname' => 'Fabrizio', 
            'lastname' => 'Castellarin',
            'username' => 'EnoahNetzach',
            'email' => 'f.castellarin@gmail.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1990, 4, 19),
            'image' => '00b7b971d96ce05797e6757e5a0a4232.jpeg'
        ),
        array('firstname' => 'Federica',
            'lastname' => 'Fontana',
            'username' => 'fede_pucci_pucci',
            'email' => 'f.fontana@gmail.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Taranto',
            'city_current' => 'Milan',
            'birth_date' => array(1900, 4, 15),
            'image' => '00b7b971d96ce05797e6757e5a0a4232.jpeg'
        ),
        array('firstname' => 'Luca',
            'lastname' => 'Casareto',
            'username' => 'ghey',
            'email' => 'lucasareto@gmail.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Padua',
            'city_current' => 'Milan',
            'birth_date' => array(190, 5, 15),
            'image' => '00b7b971d96ce05797e6757e5a0a4232.jpeg'
        ),
        array('firstname' => 'Virginia Alexandra',
            'lastname' => 'Nolte',
            'username' => 'cinnamoon',
            'email' => 'vir@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1994, 3, 29),
            'image' => '00b7b971d96ce05797e6757e5a0a4232.jpeg'
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

		for ($i = 1; $i < count($this->people) + 1; $i++) {
			$user = $userManager->createUser();
			$person = $this->people[$i - 1];
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
		  	   ->setBirthDateVisible(false)
		  	   ->setImg($person['image']);

			$manager->persist($user);
			$manager->flush();

			$this->addReference('user-'.$i, $user);
		}
	}

	public function getOrder()
	{
        return 1;
    }
}