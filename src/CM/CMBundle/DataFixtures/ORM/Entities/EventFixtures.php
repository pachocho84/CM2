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

    private static $eventsX = array(
        array('title' => 'C\'è in gioco la musica! Laboratorio musicale', 'subtitle' => '', 'extract' => 'I bambini poteranno ascoltare e suonare tutti gli strumenti dell\'Accademia! A cura dell\'Accademia musicale STABAT MATER di Rho', 'text' => 'I bambini poteranno ascoltare e suonare tutti gli strumenti dell\'Accademia! A cura dell\'Accademia musicale STABAT MATER di Rho', 'img' => '1b7e2be2de282f6cd99a91874f2f5134dd76cdd9.jpg'),
        array('title' => 'Haydn, "Stabat Mater"', 'subtitle' => '', 'extract' => 'Lurago d\'Erba si prepara a celebrare il centenario della propria chiesa prepositurale, che per l\'occasione aprirà le porte alla grande musica sacra. L\'appuntamento è per la serata di sabato prossimo, 5 ottobre.', 'text' => 'Lurago d\'Erba si prepara a celebrare il centenario della propria chiesa prepositurale, che per l\'occasione aprirà le porte alla grande musica sacra. L\'appuntamento è per la serata di sabato prossimo, 5 ottobre.', 'img' => '892eda50c9566f74859fb54a7f7912225deed5f1.jpg'),
        array('title' => 'Stagione Sinfonica: Rachmaninov & Dvořák', 'subtitle' => '', 'extract' => 'Rachmaninov - Vocalise Rachmaninov - Concerto per pianoforte e orchestra n. 2 in Do min. op. 18 Dvořák - Sinfonia n. 3 in Mi bemolle maggiore op. 10Pianoforte - Simone Pedroni 
Direttore - Aldo Ceccato', 'text' => 'Rachmaninov - Vocalise Rachmaninov - Concerto per pianoforte e orchestra n. 2 in Do min. op. 18 Dvořák - Sinfonia n. 3 in Mi bemolle maggiore op. 10Pianoforte - Simone Pedroni 
Direttore - Aldo Ceccato', 'img' => 'a702f79522d2b77a9df4f973c6997fe7379ed360.jpg'),
        array('title' => 'Concerto Straordinario: Festeggiamo Verdi!', 'subtitle' => '', 'extract' => 'Coro Sinfonico di Milano Giuseppe Verdi Maestro del Coro - Erina Gambarini Direttore - Jader Bignamini', 'text' => 'Coro Sinfonico di Milano Giuseppe Verdi Maestro del Coro - Erina Gambarini Direttore - Jader Bignamini', 'img' => 'dd6534799af0c15d82f31c29009b743c1d8515d3.jpg'),
        array('title' => 'VII Stagione di Musica da Camera', 'subtitle' => '', 'extract' => 'Giovedì 10 ottobre, ore 19 
INAUGURAZIONE DELLA VII STAGIONE DI MUSICA DA CAMERA 
UNA SERATA NON STOP CON MUSICA, TEATRO, LETTURE E FANTASIA 
A cura degli artisti dell\'Associazione Culturale ConcertArti e loro amici Dario Atzori, Giovanni Bacchelli, Giacomo Brunini, Alessio Cercignani, Alessandra Dezzi, Manuela Evangelista, Roberto Fiorini, Daniele Fredianelli, Elisabetta', 'text' => 'Giovedì 10 ottobre, ore 19 
<br />INAUGURAZIONE DELLA VII STAGIONE DI MUSICA DA CAMERA 
<br />UNA SERATA NON STOP CON MUSICA, TEATRO, LETTURE E FANTASIA
<br />A cura degli artisti dell\'Associazione Culturale ConcertArti e loro amici Dario Atzori, Giovanni Bacchelli, Giacomo Brunini, Alessio Cercignani, Alessandra Dezzi, Manuela Evangelista, Roberto Fiorini, Daniele Fredianelli, Elisabetta Furini, Laura Lorenzi, Riccardo Masi, Riccardo Mazzoni, Lucia Neri, Maila Nosiglia, Daniela Panicucci, Marco Rodi, Sonia Salvini, Eleonora Stefanini, Danila Talamo, Milena Vox
<br />Musiche di: 
<br />J. S. Bach, M. Castelnuovo – Tedesco, G. Fauré,  R. Gnattali, A. Glazunov,  E. Granados, J. Macmillan, D. Milhaud, G. Rossini, N. Rota, C. Saint-Saens, F. Schubert, O. Singer
<br />Testi di: 
<br />Franca Rame, Marco Rodi, Sonia Salvini e Elisabetta Furini, Danila Talamo, Milena Vox
<br />Ingresso: offerta a favore dell\'attività culturale di ConcertArti</p><p>Sabato 9 novembre, ore 18
<br />LA TRIO SONATA NELLA GERMANIA DEL XVIII SECOLO
<br />Ensemble Il fabbro armonioso 
<br />Gian Marco Solarolo oboe barocco, Elisa Bestetti violino barocco, Cristina Monti spinetta
<br />Musiche di: G. Finger, A. Vivaldi, F. Gasparini, G. Ph. Telemann, G. F. Haendel  </p><p>Giovedì 12 dicembre, ore 21
<br />ALLEGRO CANTABILE – Divertimenti armonici per una sera d\'inverno
<br />Coro R. Del Corona 
<br />Luca Stornello direttore
<br />Dal Rinascimento al \'900: le diverse espressioni della musica vocale </p><p>Sabato 21 dicembre, ore 21 - Chiesa di San Benedetto, piazza XX Settembre, Livorno
<br />CONCERTO DI NATALE per soli, coro e archi
<br />Accademia Vocale città di Livorno 
<br />Daniela Contessi direttore, Claire Briant soprano, Open Ensemble
<br />Musiche di: A. Vivaldi, G. Ph. Telemann, W. A. Mozart, F. Schubert, G. Fauré
<br />Ingresso libero</p><p>Sabato 1 febbraio, ore 10.30 e ore 18
<br />TI PARLERÒ D\'AMOR - Le canzoni italiane degli anni \'40
<br />Ensemble Phalèse 
<br />Nadia De Sanctis voce, Franco Barbucci violino, Lucia Goretti viola, 
<br />Laura Goretti violoncello, Marco Gammanossi Chitarra
<br />Arrangiamenti musicali: Marco Gammanossi </p><p>Giovedì 6 marzo, ore 10.30 e ore 21
<br />CLASSICA E POP: UN DIALOGO POSSIBILE - Stili, linguaggi, tecniche a confronto
<br />A cura di Marco Lenzi. Alla tastiera Claudio Laucci</p><p>Sabato 12 aprile, ore 18
<br />L\'ARTE DEL MANDOLINO - Musica per quartetto a plettro tra Ottocento e Novecento
<br />Quartetto Estudiantina Bergamo 
<br />Marina Ferrari e Chiara Perini mandolino, Mario Rota mandola, 
<br />Michele Guadalupi chitarra e mandoloncello
<br />Musiche di: C. Munier, A. Amadei, G. Sartori, R. Calace</p><p>Abbonamento per 5 concerti: 
<br />Soci Cral Eni, Coop e ConcertArti € 25; intero € 35 – Dal 1° ottobre. 
<br />Ingresso: 
<br />Studenti € 3 – Docenti accompagnatori, gratuito ogni 8 studenti - 
<br />Soci Cral Eni, Coop e ConcertArti € 6 - Intero € 8 - 
<br />Prevendita a partire da 7 giorni prima del concerto presso Teatro Cral Eni Livorno Viale Ippolito Nievo 38
<br />Prenotazioni tel. 0586 401308 - Orario: 8 / 12 - 15 / 19 
<br />Informazioni concertarti@gmail.com</p><p>Ringraziamo la Fondazione Cassa di Risparmi di Livorno per il contributo concesso ed 
<br />il Comitato Soci Livorno UNICOOP Tirreno per il buffet offerto.</p><p>Organizzato da Cral Eni Livorno in collaborazione con Associazione Culturale ConcertArti, Dir. Artistica Renata Sfriso', 'img' => 'f2d3a3af3c6cdc40aab224f59e99d2ea1ec85afa.jpg'),
    );

    private static $events = array(
        array(
            'title'     => 'Quartetto di Cremona - Esecuzione integrale dei Quartetti per archi di Beethoven - V', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Esecuzione integrale dei Quartetti per archi di Beethoven - VL. van Beethoven ‐ Quartetto n. 1 in fa maggiore op. 18 n. 1<br/>L. van Beethoven ‐ Grande Fuga in si bemolle maggiore op. 133<br/>L. van Beethoven ‐ Quartetto n. 2 in sol maggiore op. 18 n.', 
            'img'       => 'quartetto_cremona.jpg',
            'dates'     => 1,
            'page'      => 2,
        ),
        array(
            'title'     => 'F.J. Haydn ‐ "La Creazione", Orchestre des Champs-Elysées - Collegium Vocale Gent - Philippe Herreweghe', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Orchestre des Champs-ElyséesCollegium Vocale GentPhilippe Herreweghe direttoreChristina Landshamer soprano<br/>Maximilian Schmitt tenore<br/>Rudolf Rosen bassoF.J. Haydn ‐ Die Schöpfung Hob.XXI.', 
            'img'       => 'herreweghe.jpg',
            'dates'     => 1,
            'page'      => 2,
        ),
        array(
            'title'     => 'Andrea Lucchesini, pianoforte', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Andrea Lucchesini, pianoforteW.A. Mozart ‐ Sonata in sol maggiore K 283<br/>F. Schubert ‐ 3 Klavierstücke D 946<br/>J. Brahms ‐ 3 Intermezzi op. 117<br/>R. Strauss ‐ Sonata in si minore op. 5Vocazione europea, radici italiane: questo potrebbe essere considerato da sempre il motto del Quartetto.', 
            'img'       => 'andrea_lucchesini.jpg',
            'dates'     => 1,
            'page'      => 2,
        ),
        array(
            'title'     => '24. Stagione Sinfonica: una prima assoluta e Mahler', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Vacchi - Veronica Franco, per soprano, voce recitante e orchestra ( commissione de laVerdi )<br/>Mahler - Sinfonia n. 10 in Fa diesis maggiore ( versione Barshai )Direttore - Claire Gibault', 
            'img'       => 'laverdi_xian_zhang.jpg',
            'dates'     => 1,
            'page'      => 3,
        ),
        array(
            'title'     => '4. Domenica mattina con laVerdi: Luigi Dallapiccola (1904-1975)', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Luigi Dallapiccola (1904-1975)Bartók - Sonata per due pianoforti e percussioni<br/>Dallapiccola - Piccola musica notturna<br/>Copland - Appalachian SpringDirettore - Giuseppe Grazioli', 
            'img'       => 'laverdi_dallapiccola.jpg',
            'dates'     => 1,
            'page'      => 3,
        ),
        array(
            'title'     => '5. LaBarocca: Locatelli & Vivaldi', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Locatelli - Concerto Grosso n. 2 in Do minore op. 1<br/>Locatelli - Concerto n. 2 per violino, archi e basso continuo in Do minore op. 3 “L’arte del violino”<br/>Locatelli - Concerto Grosso n. 5 in Re maggiore op. 1<br/>Vivaldi - Concerto per violoncello in Re minore RV 406<br/>Locatelli - Concerto Grosso n. 12 in Sol minore op.', 
            'img'       => 'laverdi_locatelli_vivaldi.jpg',
            'dates'     => 1,
            'page'      => 3,
        ),
        array(
            'title'     => 'Presentazione disco di Sol Gabetta', 
            'subtitle'  => '', 
            'extract'   => '', 
            'text'      => 'Locatelli - Concerto Grosso n. 2 in Do minore op. 1<br/>Locatelli - Concerto n. 2 per violino, archi e basso continuo in Do minore op. 3 “L’arte del violino”<br/>Locatelli - Concerto Grosso n. 5 in Re maggiore op. 1<br/>Vivaldi - Concerto per violoncello in Re minore RV 406<br/>Locatelli - Concerto Grosso n. 12 in Sol minore op.', 
            'img'       => 'sol_gabetta.jpg',
            'user'      => 5,
            'dates'     => 1,
            'sponsored' => true,
            
        )
    );
    
    private $locations = array(
        array('Auditorium di Milano', 'Largo Mahler, 10136 Milano', '45.446592,9.179087'),
        array('Teatro alla Scala', 'Via Filodrammatici, 2, 10121 Milano', '45.467402,9.189551'),
        array('Teatro dell\'Elfo', 'Corso Buenos Aires, 33, 10124 Milano', '45.479404,9.209745'),
        array('Piccolo Teatro', 'Largo Antonio Greppi, 1, 10121 Milano', '45.472337,9.182449'),
        array('Teatro degli Arcimboldi', 'Viale dell\'Innovazione, 20, 10125 Milano', '45.51170,9.21109'),
    );

    private $images = array('bb01acb97854b24ed23598bd4f055eba.jpeg', 'ff9398d3d47436e2b4f72874a2c766fd.jpeg');

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

        $manager->persist($event);

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
            $eventDate->setLocation($this->locations[$locNum][0]);
            $eventDate->setAddress($this->locations[$locNum][1]);
            $eventDate->setCoordinates($this->locations[$locNum][2]);
            $event->addEventDate($eventDate);
        }

        $page = null;
        $group = null;
        if (array_key_exists('page', EventFixtures::$events[$i])) {
            $page = $manager->merge($fixture->getReference('page-'.EventFixtures::$events[$i]['page']));
            $user = $page->getCreator();
        } elseif (array_key_exists('group', EventFixtures::$events[$i])) {
            $group = $manager->merge($fixture->getReference('page-'.EventFixtures::$events[$i]['group']));
            $user = $group->getCreator();
        }
        if (array_key_exists('user', EventFixtures::$events[$i])) {
            $user = $manager->merge($fixture->getReference('user-'.EventFixtures::$events[$i]['user']));
        }

        /* Image */
/*
        if (rand(0, 8) > 0) {
            $image = new Image;
            $image->setImg(EventFixtures::$events[$i]['img'])
                ->setText('main image for event "'.$event->getTitle().'"')
                ->setMain(true)
                ->setUser($user);
            $event->setImage($image);

            $manager->persist($event);
            $manager->flush();               

            for ($j = rand(1, 4); $j > 0; $j--) {
                $image = new Image;
                $image
                    ->setImg(EventFixtures::$events[$i]['img'])
                    ->setText('image number '.$j.' for event "'.$event->getTitle().'"')
                    ->setMain(false)
                    ->setUser($user);
                
                $event->addImage($image);
            }
        }
*/

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
        
        /* Category */
        $category = $manager->merge($fixture->getReference('event_category-'.rand(1, 3)));
        $category->addEntity($event);

        $post = $this->container->get('cm.post_center')->getNewPost($user, $user);
        $manager->persist($post);
        $post->setPage($page);
        $post->setGroup($group);

        $event->setPost($post);
        
        $userTags = array();
        for ($j = 1; $j < rand(1, 3); $j++) {
            $userTags[] = $manager->merge($fixture->getReference('user_tag-'.rand(1, 10)));
        }
        
        $event->addUser(
            $user,
            true, // admin
            EntityUser::STATUS_ACTIVE,
            true, // notification
            $userTags
        );

        $numbers = range(1, ORM\UserFixtures::count());
        shuffle($numbers);
        for ($j = 0; $j < rand(0, 6); $j++) {
            $otherUser = $manager->merge($fixture->getReference('user-'.$numbers[$j]));
            if ($otherUser == $user) continue;
            
            $userTags = array();
            for ($k = 1; $k < rand(1, 3); $k++) {
                $userTags[] = $manager->merge($fixture->getReference('user_tag-'.rand(1, 10)));
            }

            $event->addUser(
                $otherUser,
                !rand(0, 3), // admin
                EntityUser::STATUS_PENDING,
                true, // notification
                $userTags
            );
        }
        
        /* Main Image */
        $image = new Image;
        $image->setImg(EventFixtures::$events[$i]['img'])
            ->setText('main image for event "'.$event->getTitle().'"')
            ->setMain(true)
            ->setUser($user);
        $event->setImage($image);

        $manager->persist($image);
        
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

        var_dump('event-'.($i + 1));
        $fixture->addReference('event-'.($i + 1), $event);
    }
}