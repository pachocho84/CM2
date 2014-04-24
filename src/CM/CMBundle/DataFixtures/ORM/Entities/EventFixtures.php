<?php

namespace CM\CMBundle\DataFixtures\ORM\Entities;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Multimedia;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\Entity\Sponsored;
use CM\CMBundle\DataFixtures\ORM;

class EventFixtures
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private static $events = array(
        array(
            'title'     => 'Quartetto di Cremona - Esecuzione integrale dei Quartetti per archi di Beethoven - V', 
            'subtitle'  => '', 
            'extract'   => null, 
            'text'      => 'Esecuzione integrale dei Quartetti per archi di Beethoven - VL. van Beethoven ‐ Quartetto n. 1 in fa maggiore op. 18 n. 1
L. van Beethoven ‐ Grande Fuga in si bemolle maggiore op. 133
L. van Beethoven ‐ Quartetto n. 2 in sol maggiore op. 18 n.', 
            'img'       => 'quartetto_cremona.jpg',
            'dates'     => 1,
            'page'      => 2,
        ),
        array(
            'title'     => 'F.J. Haydn ‐ "La Creazione", Orchestre des Champs-Elysées - Collegium Vocale Gent - Philippe Herreweghe', 
            'subtitle'  => '', 
            'extract'   => null, 
            'text'      => 'Orchestre des Champs-ElyséesCollegium Vocale GentPhilippe Herreweghe direttoreChristina Landshamer soprano
Maximilian Schmitt tenore
Rudolf Rosen bassoF.J. Haydn ‐ Die Schöpfung Hob.XXI.', 
            'img'       => 'herreweghe.jpg',
            'dates'     => 1,
            'page'      => 2,
        ),
        array(
            'title'     => 'Andrea Lucchesini, pianoforte', 
            'subtitle'  => '', 
            'extract'   => null, 
            'text'      => 'Andrea Lucchesini, pianoforteW.A. Mozart ‐ Sonata in sol maggiore K 283
F. Schubert ‐ 3 Klavierstücke D 946
J. Brahms ‐ 3 Intermezzi op. 117
R. Strauss ‐ Sonata in si minore op. 5Vocazione europea, radici italiane: questo potrebbe essere considerato da sempre il motto del Quartetto.', 
            'img'       => 'andrea_lucchesini.jpg',
            'dates'     => 1,
            'page'      => 2,
        ),
        array(
            'title'     => '24. Stagione Sinfonica: una prima assoluta e Mahler', 
            'subtitle'  => '', 
            'extract'   => null,
            'text'      => 'Vacchi - Veronica Franco, per soprano, voce recitante e orchestra (commissione de laVerdi)
Mahler - Sinfonia n. 10 in Fa diesis maggiore ( versione Barshai )Direttore - Claire Gibault', 
            'img'       => 'laverdi_xian_zhang.jpg',
            'dates'     => 1,
            'page'      => 3,
        ),
        array(
            'title'     => '4. Domenica mattina con laVerdi: Luigi Dallapiccola (1904-1975)', 
            'subtitle'  => '', 
            'extract'   => null, 
            'text'      => 'Luigi Dallapiccola (1904-1975)Bartók - Sonata per due pianoforti e percussioni
Dallapiccola - Piccola musica notturna
Copland - Appalachian SpringDirettore - Giuseppe Grazioli', 
            'img'       => 'laverdi_dallapiccola.jpg',
            'dates'     => 1,
            'page'      => 3,
        ),
        array(
            'title'     => '5. LaBarocca: Locatelli & Vivaldi', 
            'subtitle'  => '', 
            'extract'   => null, 
            'text'      => 'Locatelli - Concerto Grosso n. 2 in Do minore op. 1
Locatelli - Concerto n. 2 per violino, archi e basso continuo in Do minore op. 3 “L’arte del violino”
Locatelli - Concerto Grosso n. 5 in Re maggiore op. 1
Vivaldi - Concerto per violoncello in Re minore RV 406
Locatelli - Concerto Grosso n. 12 in Sol minore op.', 
            'img'       => 'laverdi_locatelli_vivaldi.jpg',
            'dates'     => 1,
            'page'      => 3,
        ),
        array(
            'title'     => 'Presentazione disco di Sol Gabetta', 
            'subtitle'  => '', 
            'extract'   => null, 
            'text'      => 'Locatelli - Concerto Grosso n. 2 in Do minore op. 1
Locatelli - Concerto n. 2 per violino, archi e basso continuo in Do minore op. 3 “L’arte del violino”
Locatelli - Concerto Grosso n. 5 in Re maggiore op. 1
Vivaldi - Concerto per violoncello in Re minore RV 406
Locatelli - Concerto Grosso n. 12 in Sol minore op.', 
            'img'       => 'sol_gabetta.jpg',
            'dates'     => 1,
            'sponsored' => true, 
            'page'      => 1,
        ),
        array(
            'title'     => 'Madesimo Music Festival', 
            'subtitle'  => '', 
            'extract'   => '<p><strong>Quattordici emozionanti appuntamenti</strong> all’insegna della musica classica riempiranno il paese di cultura e vivacità: dalla musica barocca con l’oboe d’avorio di Simone Toni e l’ensemble “Silete Venti!” alla celebrazione del duecentesimo anniversario di Wagner e Verdi con i due workshop di Orazio Sciortino e Alessio Bidoli.</p>', 
            'text'      => '<p>12 Luglio 2013 
ore 10:00 - 
Comune di Madesimo  
<span class=\"piccolo\">(Madesimo SO, Italia)</span>
</p>

<p>Debutta quest’anno il <strong>Madesimo Music Festival</strong>, nato dalla collaborazione tra Sony Classical Italia e il Comune di Madesimo. Il Festival si svolgerà nei luoghi caratteristici di Madesimo <strong>dal 12 al 31 luglio</strong> e vedrà esibirsi artisti di fama internazionale.</p>
<p><strong><br></strong></p>
<p><strong>Quattordici emozionanti appuntamenti</strong> all’insegna della musica classica riempiranno il paese di cultura e vivacità: dalla musica barocca con l’oboe d’avorio di Simone Toni e l’ensemble “Silete Venti!” alla celebrazione del duecentesimo anniversario di Wagner e Verdi con i due workshop di Orazio Sciortino e Alessio Bidoli.</p>
<p>Il tema di questa prima edizione è <strong>“Costruire il talento”</strong> e si inserisce in una serie di iniziative che il Comune di Madesimo sta portando avanti in una prospettiva di crescita sociale che abbraccia tutti gli ambiti. Un Festival insomma che volge il suo sguardo al territorio e al futuro.</p>
<p>Apre il Festival la consegna del “Premio Madesimo” a Mario Marcarini, label manager di Sony Classical Italia e direttore artistico di questa prima edizione. Motivazione: la letteratura applicata alla storia della musica. Marcarini in questa occasione propone un workshop dedicato a grandi e piccini: “Wolfgang e Leopold”, Mozart figlio e padre, la nascita di un talento.</p>
<p>Madesimo Music Festival 2013 è realizzato con il sostegno e la collaborazione del Comune di Madesimo, Sony Classical Italia, Opificio Italiano dei Classici, Fazioli, Swing, Ricola, Rivista Musica, Radio Classica e Circuito Musica.</p>
<p><strong><br></strong></p>
<p><strong>INFO STAMPA:</strong></p>
<p><strong><br></strong></p>
<p><strong>Ufficio Informazioni Madesimo</strong></p>
<p>Via alle Scuole - Madesimo (SO) Telefono +39 0343 53015</p>', 
            'img'       => 'madesimo.jpg',
            'dates'     => 1,
            'sponsored' => true, 
            'page'      => 1,
        ),
    );
    
    private $locations = array(
        array('Auditorium di Milano', 'Largo Mahler, 10136 Milano', '45.446592', '9.179087'),
        array('Teatro alla Scala', 'Via Filodrammatici, 2, 10121 Milano', '45.467402', '9.189551'),
        array('Teatro dell\'Elfo', 'Corso Buenos Aires, 33, 10124 Milano', '45.479404', '9.209745'),
        array('Piccolo Teatro', 'Largo Antonio Greppi, 1, 10121 Milano', '45.472337', '9.182449'),
        array('Teatro degli Arcimboldi', 'Viale dell\'Innovazione, 20, 10125 Milano', '45.51170', '9.21109'),
    );

    public static function count()
    {
        return count(EventFixtures::$events);
    } 

    /**
     * {@inheritDoc}
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(AbstractFixture $fixture, ObjectManager $manager, $i, $infoes)
    {
        $event = new Event;
        $event->setTitle(EventFixtures::$events[$i]['title'])
            ->setExtract(EventFixtures::$events[$i]['extract'])
            ->setText(EventFixtures::$events[$i]['text']);

        /* Translations */
        if (0 == rand(0, 2)) {
            $event->translate('it')
                ->setTitle(EventFixtures::$events[$i]['title'])
                ->setExtract(EventFixtures::$events[$i]['extract'])
                ->setText(EventFixtures::$events[$i]['text']);
        }

/*
        if (0 == rand(0, 4)) {
            $event->translate('fr')
                ->setTitle(EventFixtures::$events[$i]['title'].' (fr)')
                ->setExtract(EventFixtures::$events[$i]['extract'])
                ->setText(EventFixtures::$events[$i]['text']);
        }
*/

        $event->mergeNewTranslations();
        
        /* Category */
        $category = $manager->merge($fixture->getReference('event_category-'.rand(1, 3)));
        $event->setCategory($category);
           
        /* Dates */
        for ($j = EventFixtures::$events[$i]['dates']; $j > 0; $j--) {
            $eventDate = new EventDate;
            $dtz = new \DateTime;
            $dtz->setTimestamp(rand(time() - 3155692, time() + 31556926));
            $dtz->setTimeZone(new \DateTimeZone('Europe/Berlin'));
            $eventDate->setStart($dtz);
        
            if (rand(0, 1) == 0) {
                $dtz->setTimestamp($eventDate->getStart()->getTimestamp() + 7200);
                $eventDate->setEnd($dtz);
            }
        
            $locNum = rand(0, 4);
            $eventDate->setLocation($this->locations[$locNum][0])
                ->setAddress($this->locations[$locNum][1])
                ->setLatitude($this->locations[$locNum][2])
                ->setLongitude($this->locations[$locNum][3]);
            $event->addEventDate($eventDate);
        }

        /* Post */
        $page = null;
        if (array_key_exists('page', EventFixtures::$events[$i])) {
            $page = $manager->merge($fixture->getReference('page-'.EventFixtures::$events[$i]['page']));
            $user = $page->getCreator();
        } elseif (array_key_exists('user', EventFixtures::$events[$i])) {
            $user = $manager->merge($fixture->getReference('user-'.EventFixtures::$events[$i]['user']));
        }

        $post = $this->container->get('cm.post_center')->getNewPost($user, $user);
        $manager->persist($post);
        $post->setPage($page);

        $event->setPost($post);
        
        /* Protagonists */
        $tags = array();
        for ($k = 1; $k < rand(1, 3); $k++) {
            $tags[] = $manager->merge($fixture->getReference('tag-'.rand(1, 10)));
        }
        
        $event->addUser(
            $user,
            true, // admin
            EntityUser::STATUS_ACTIVE,
            true, // notification
            $tags
        );

        $numbers = range(1, ORM\UserFixtures::count());
        shuffle($numbers);
        for ($j = 0; $j < rand(0, 6); $j++) {
            $otherUser = $manager->merge($fixture->getReference('user-'.$numbers[$j]));
            if ($otherUser == $user) continue;
            
            $tags = array();
            for ($k = 1; $k < rand(1, 3); $k++) {
                $tags[] = $manager->merge($fixture->getReference('tag-'.rand(1, 10)));
            }

            $event->addUser(
                $otherUser,
                !rand(0, 3), // admin
                EntityUser::STATUS_PENDING,
                true, // notification
                $tags
            );
        }
        
        /* Main Image */
        $image = new Image;
        $image->setImg(EventFixtures::$events[$i]['img'])
            ->setText('main image for event "'.$event->getTitle().'"')
            ->setMain(true)
            ->setUser($user);
        $event->setImage($image);

        /* Multimedia */
/*
        for ($j = 0; $j < rand(0, 8); $j++) {
            $info = $infoes[rand(0, count($infoes) - 1)];

            $multimedia = new Multimedia;
            $multimedia->setType($info['type']);
            $multimedia->setSource($info['source']);
            $multimedia->setTitle($info['info']->title)
                ->setText($info['info']->description);

            $event->addMultimedia($multimedia);
        }
*/
        
        /* Sponsored */
        if (array_key_exists('sponsored', EventFixtures::$events[$i])&& EventFixtures::$events[$i]['sponsored'] == true) {
            $sponsored = new Sponsored;
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

        $manager->persist($event);
        
        if ($i % 10 == 9) {
            $manager->flush();
        }

        $fixture->addReference('event-'.($i + 1), $event);
    }
}