<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use CM\CMBundle\Entity\Event;
use CM\CMBundle\Entity\EventDate;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\User;

class EventFixtures extends AbstractFixture implements OrderedFixtureInterface
{
    private $events = array(
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
    
    private $locations = array(
        array('Auditorium di Milano', 'Largo Mahler, 20136 Milano', '45.446592,9.179087'),
        array('Teatro alla Scala', 'Via Filodrammatici, 2, 20121 Milano', '45.467402,9.189551'),
        array('Teatro dell\'Elfo', 'Corso Buenos Aires, 33, 20124 Milano', '45.479404,9.209745'),
        array('Piccolo Teatro', 'Largo Antonio Greppi, 1, 20121 Milano', '45.472337,9.182449'),
        array('Teatro degli Arcimboldi', 'Viale dell\'Innovazione, 20, 20125 Milano', '45.51170,9.21109'),
    );

    private $images = array('bb01acb97854b24ed23598bd4f055eba.jpeg', 'ff9398d3d47436e2b4f72874a2c766fd.jpeg');

    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i < 201; $i++) {
            $eventNum = rand(0, count($this->events) - 1);
            $event = new Event;
            $event->setVisible(rand(0, 1));
            $event->setTitle($this->events[$eventNum]['title'].' (en)')
                ->setExtract($this->events[$eventNum]['extract'])
                ->setText($this->events[$eventNum]['text']);
    
            if (0 == rand(0, 2)) {
                $event->translate('it')
                    ->setTitle($this->events[$eventNum]['title'].' (it)')
                    ->setExtract($this->events[$eventNum]['extract'])
                    ->setText($this->events[$eventNum]['text']);
            }
    
            if (0 == rand(0, 4)) {
                $event->translate('fr')
                    ->setTitle($this->events[$eventNum]['title'].' (fr)')
                    ->setExtract($this->events[$eventNum]['extract'])
                    ->setText($this->events[$eventNum]['text']);
            }
    
    /*
            $event->translate('ru')->setTitle('Печатное (RU) '.$i)
                ->setSubtitle('Субти́тр (RU) '.$i)
                ->setExtract('Экстракта (RU) '.$i)
                ->setText('Текст (RU) '.$i);
    */
               
            for ($j = rand(1, 3); $j > 0; $j--) {
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
            
            if (rand(0, 4) > 0) {

                $user = $manager->merge($this->getReference('user-'.rand(1, 5)));

                $image = new Image;
                $image
                    ->setImg($this->events[$eventNum]['img'])
                    ->setText('main image for event "'.$event->getTitle().'"')
                    ->setMain(true)
                    ->setUser($user);
                $event->addImage($image);                
    
                for ($j = rand(1, 4); $j > 0; $j--) {
                    $image = new Image;
                    $image
                        ->setImg($this->events[$eventNum]['img'])
                        ->setText('image number '.$j.' for event "'.$event->getTitle().'"')
                        ->setMain(false)
                        ->setUser($user);
                    
                    $event->addImage($image);
                }
    
            }
            
            $user = $manager->merge($this->getReference('user-'.rand(1, 5)));
            
            $category = $manager->merge($this->getReference('entity_category-'.rand(1, 3)));
            $category->addEntity($event);

            $post = new Post;
            $post
                ->setType(Post::TYPE_CREATION)
                ->setUser($user);

            $event->addPost($post);
            
            $manager->persist($event);
            $event->mergeNewTranslations();

            $this->addReference('event-'.$i, $event);
            
            if ($i % 10 == 0) {
                echo $i." - ";
                $manager->flush();
            }
        }
    
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 3;
    }
}