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
use CM\CMBundle\Entity\EntityCategoryEnum;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
	private $images = array('ff9398d3d47436e2b4f72874a2c766fd.jpeg', '00b7b971d96ce05797e6757e5a0a4232.jpeg');
	
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

		$user = $userManager->createUser();
		$date = new \DateTime;
		$user
			->setEmail('ernesto@circuitomusica.it')
			->setUsername('pachocho')
			->setPlainPassword('pacho')
			->setEnabled(true)
			->setFirstName('Ernesto')
			->setLastName('Casareto')
			->setSex(User::SEX_M)
			->setCityBirth('Padua')
			->setCityCurrent('Milan')
			->setBirthDate($date->setDate(1984, 5, 17))
			->setBirthDateVisible(false)
			->setImg($this->images[1]);

		$manager->persist($user);
		$manager->flush();

		for ($i = 1; $i < 11; $i++) {
			$userNum = rand(0, count($this->images) - 1);

			$user = $userManager->createUser();

			$date = new \DateTime;
			$date->setTimestamp(rand(time() - 600000000, time() + 1000000000));

			$user
				->setEmail('test'.$i.'@gmail.com')
				->setUsername('user '.$i)
				->setPlainPassword('test')
				->setEnabled(true)
				->setFirstName('Name '.$i)
				->setLastName('Lastame '.$i)
				->setSex(rand(0, 1) ? User::SEX_M : User::SEX_F)
				->setCityBirth('Milan')
				->setCityCurrent('Milan')
				->setBirthDate($date)
				->setBirthDateVisible(rand(0, 1) ? true : false)
				->setImg($this->images[$userNum]);

			$manager->persist($user);
			$manager->flush();

			$this->addReference('user-'.$i, $user);
		}
	}

	public function getOrder()
	{
        return 2;
    }
}