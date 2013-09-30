<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use CM\CMBundle\Entity\EntityTranslation;
use CM\CMBundle\Entity\Entity;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;

class EventFixtures extends AbstractFixture implements OrderedFixtureInterface
{
	private $locations = array(
		array('Auditorium di Milano', 'Largo Mahler, 20136 Milano', '45.446592,9.179087'),
		array('Teatro alla Scala', 'Via Filodrammatici, 2, 20121 Milano', '45.467402,9.189551'),
		array('Teatro dell\'Elfo', 'Corso Buenos Aires, 33, 20124 Milano', '45.479404,9.209745'),
		array('Piccolo Teatro', 'Largo Antonio Greppi, 1, 20121 Milano', '45.472337,9.182449'),
		array('Teatro degli Arcimboldi', 'Viale dell\'Innovazione, 20, 20125 Milano', '45.51170,9.21109'),
	);

    public function load(ObjectManager $manager)
    {
       	for ($i = 1; $i < 101; $i++) {
	   		$event = new Event;
           	$event->setTitle('Title (EN) '.$i)
           	    ->setSubtitle('Subtitle (EN) '.$i)
           	    ->setExtract('Extract (EN) '.$i)
           	    ->setText('Text (EN) '.$i)
           	    ->setVisible(true)
           	    ->addTranslation(new EntityTranslation('ru', 'title', 'Печатное (RU) '.$i))
           	    ->addTranslation(new EntityTranslation('ru', 'subtitle', 'Субти́тр (RU) '.$i))
           	    ->addTranslation(new EntityTranslation('ru', 'extract', 'Экстракта (RU) '.$i))
           	    ->addTranslation(new EntityTranslation('ru', 'text', 'Текст (RU) '.$i));

           	if ($i % 10 == rand(0, 9)) {
           	$event->addTranslation(new EntityTranslation('it', 'title', 'Titolo (IT) '.$i))
           	    ->addTranslation(new EntityTranslation('it', 'subtitle', 'Sottotitolo (IT) '.$i))
           	    ->addTranslation(new EntityTranslation('it', 'extract', 'Estratto (IT) '.$i))
           	    ->addTranslation(new EntityTranslation('it', 'text', 'Testo (IT) '.$i));
           	}
           	
           	for ($j = rand(1, 3); $j > 0; $j--) {
	           	$eventDate = new EventDate;
	           	$dtz = new \DateTime();
	           	$dtz->setTimestamp(rand(time(), time() + 31556926));
	           	$dtz->setTimeZone(new \DateTimeZone('Europe/Berlin'));
			   	$eventDate->setStart($dtz);
			   	if (rand(0, 1) == 0) {
			   		$dtz->setTimestamp($eventDate->getStart()->getTimestamp() + 7200);
			   		$eventDate->setEnd($dtz);
			    }
			   	$locNum = rand(0, 4);
	           	$eventDate->setLocation($this->locations[$locNum][0]);
	           	$eventDate->setAddress($this->locations[$locNum][1]);
	           	$eventDate->setCoordinates($this->locations[$locNum][2]);
	           	$event->addEventDate($eventDate);
           	}
           	
           	$manager->persist($event);
           	
           	if ($i % 10 == 0) {
           		echo '.';
           	    $manager->flush();
           	}
       	}

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }
}