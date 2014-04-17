<?php

namespace CM\CMBundle\DataFixtures\ORM\Entities;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Disc;
use CM\CMBundle\Entity\DiscTrack;
use CM\CMBundle\Entity\Image;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Like;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\EntityUser;
use CM\CMBundle\DataFixtures\ORM;

class DiscFixtures
{
    /**
     * @var ContainerInterface
     */
    private $container;

    private static $discs = array(
        array('title' => 'Vittorio Grigolo: Ave Maria',
            'label' => 'Sony Classical Italia',
            'year' => '2013-1-1',
            'discTracks' => array(
                array('composer'    => 'Giovanni Maria Catena',
                    'title'         => 'Ave Maria',
                    'movement'      => 'I',
                    'artists'       => 'Vittorio Grigolo, tenore; Orchestra Roma Sinfonietta; Pueri Cantores della Cappella Musicale Pontificia detta Sistina; Fabio Cerroni, conductor; Fabio Cerroni, organ',
                    'duration'      => '0:03:20'
                ),
                array('composer'    => 'Giovanni Maria Catena',
                    'title'         => 'Fermarono i cieli (antica melodia napoletana)',
                    'movement'      => 'I',
                    'artists'       => 'Vittorio Grigolo, tenore; Orchestra Roma Sinfonietta; Pueri Cantores della Cappella Musicale Pontificia detta Sistina; Fabio Cerroni, conductor; Fabio Cerroni, organ',
                    'duration'      => '0:03:44'
                ),
                array('composer'    => 'Campetti',
                    'title'         => 'Maria, che dolce nome',
                    'movement'      => 'I',
                    'artists'       => 'Vittorio Grigolo, tenore; Orchestra Roma Sinfonietta; Pueri Cantores della Cappella Musicale Pontificia detta Sistina; Fabio Cerroni, conductor; Fabio Cerroni, organ',
                    'duration'      => '0:03:43'
                ),
            ),
            'text'  => 'Noto in tutto il mondo per la sua sublime voce tenorile e una passionale presenza di palcoscenico, Vittorio Grigòlo ha imparato il suo mestiere come cantore nel leggendario coro della Cappella Sistina in Vaticano, la culla della musica sacra occidentale. Ispirandosi all’epoca contemporanea, con quello stesso coro che foggiò i suoi esordi musicali, egli presenta ora un’intima collezione di canti spirituali splendidi e celestiali.',
            'img'   => 'grigolo_ave_maria.jpg',
            'page'  => 1
        ),
        array('title' => 'Cruel Beauty - Trascrizioni per pianoforte da musiche antiche',
            'label' => 'Sony Classical Italia',
            'year' => '2013-1-1',
            'discTracks' => array(
                array('composer'    => 'Bernardo Pasquini',
                    'title'         => 'Toccata del secondo tono',
                    'movement'      => null,
                    'artists'       => 'Giuseppe Andaloro, pianoforte',
                    'duration'      => '0:01:37'
                ),
                array('composer'    => 'Girolamo Frescobaldi',
                    'title'         => 'Toccata seconda',
                    'movement'      => null,
                    'artists'       => 'Giuseppe Andaloro, pianoforte',
                    'duration'      => '0:03:23'
                ),
                array('composer'    => 'Giovanni Pierluigi Da Palestrina',
                    'title'         => 'Ricercare dei primi toni',
                    'movement'      => null,
                    'artists'       => 'Giuseppe Andaloro, pianoforte',
                    'duration'      => '0:05:01'
                ),
                array('composer'    => 'Tarquinio Merula',
                    'title'         => 'Sonata cromatica',
                    'movement'      => null,
                    'artists'       => 'Giuseppe Andaloro, pianoforte',
                    'duration'      => '0:04:47'
                ),
            ),
            'text'  => 'Secondo alcuni la musica non ha spazio, non ha tempo, espressione profondissima dell\'animo umano e proprio per questo fruibile addirittura in modo astratto, senza strumenti e senza parole, con la sola partitura: in altri termini, leggendo la musica sullo spartito, la si può far risuonare, pur se muta, dentro di noi, fruendo così dei valori assoluti in essa contenuti.
Secondo altri non è affatto così e la musica è invece ben radicata nel tempo e nello spazio di chi la crea ed in cui è nata: non sarebbe cioè pienamente fruibile senza avere ben presente il momento storico, la società dalla quale e per la quale venne scritta.
E in fondo queste riflessioni si ripropongono nel momento stesso in cui facciamo scivolare questo CD nel lettore: un pianoforte gran coda Fazioli è lo strumento utilizzato dal talentuosissimo Giuseppe Andaloro per proporci un programma di musiche italiane, vocali e
strumentali, cinque-seicentesche.
Frescobaldi e Pasquini, Gabrieli e Merula, Palestrina e Valente vengono letti e proposti con un pianoforte contemporaneo, rendendo forse così ragione al titolo "bellezza crudele" del CD e col quale gli ascoltatori sono chiamati a misurarsi.
 
Angelo Formenti',
            'img'   => 'andaloro_cruel_beauty.jpg',
            'page'  => 1
        ),
        array('title' => 'Bruckner: Symphony No. 7 In E Major',
            'label' => 'Sony Classical Italia',
            'year' => '2013-1-1',
            'discTracks' => array(
                array('composer'    => 'Anton Bruckner',
                    'title'         => 'Symphony No. 7 In E Major: I. Allegro Moderato',
                    'movement'      => '1',
                    'artists'       => 'Kent Nagano',
                    'duration'      => '0:20:26'
                ),
                array('composer'    => 'Anton Bruckner',
                    'title'         => 'Symphony No. 7 In E Major: II. Adagio. Sehr Feierlich Und Sehr Langsam',
                    'movement'      => '2',
                    'artists'       => 'Kent Nagano',
                    'duration'      => '0:22:07'
                ),
                array('composer'    => 'Anton Bruckner',
                    'title'         => 'Symphony No. 7 In E Major: III. Scherzo. Sehr Schnell - Trio. Etwas Langsamer',
                    'movement'      => '3',
                    'artists'       => 'Kent Nagano',
                    'duration'      => '0:09:53'
                ),
                array('composer'    => 'Anton Bruckner',
                    'title'         => 'Symphony No. 7 In E Major: IV. Finale. Bewegt, Doch Nicht Schnell',
                    'movement'      => '4',
                    'artists'       => 'Kent Nagano',
                    'duration'      => '0:12:29'
                ),
            ),
            'text'  => 'Secondo alcuni la musica non ha spazio, non ha tempo, espressione profondissima dell\'animo umano e proprio per questo fruibile addirittura in modo astratto, senza strumenti e senza parole, con la sola partitura: in altri termini, leggendo la musica sullo spartito, la si può far risuonare, pur se muta, dentro di noi, fruendo così dei valori assoluti in essa contenuti.
Secondo altri non è affatto così e la musica è invece ben radicata nel tempo e nello spazio di chi la crea ed in cui è nata: non sarebbe cioè pienamente fruibile senza avere ben presente il momento storico, la società dalla quale e per la quale venne scritta.
E in fondo queste riflessioni si ripropongono nel momento stesso in cui facciamo scivolare questo CD nel lettore: un pianoforte gran coda Fazioli è lo strumento utilizzato dal talentuosissimo Giuseppe Andaloro per proporci un programma di musiche italiane, vocali e
strumentali, cinque-seicentesche.
Frescobaldi e Pasquini, Gabrieli e Merula, Palestrina e Valente vengono letti e proposti con un pianoforte contemporaneo, rendendo forse così ragione al titolo "bellezza crudele" del CD e col quale gli ascoltatori sono chiamati a misurarsi.
 
Angelo Formenti',
            'img'   => 'nagano_bruckner.jpg',
            'page'  => 1
        ),
        array('title' => 'The Italian Bach',
            'label' => 'Sony Classical Italia',
            'year' => '2013-1-1',
            'discTracks' => array(
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Capriccio sulla lontananza del suo fratello dilettissimo, BWV: Arioso - Adagio',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Capriccio sulla lontananza del suo fratello dilettissimo, BWV: Andante',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Capriccio sulla lontananza del suo fratello dilettissimo, BWV: Adagiosissimo',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Capriccio sulla lontananza del suo fratello dilettissimo, BWV: Recitativo',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Capriccio sulla lontananza del suo fratello dilettissimo, BWV: Aria di Postiglione',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Capriccio sulla lontananza del suo fratello dilettissimo, BWV: Fuga all\'imitazione della cornetta di Potiglione',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Aria Variata alla maniera italiana: Aria',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22'
                ),
                array('composer'    => 'Johann Sebastian Bach',
                    'title'         => 'Aria Variata alla maniera italiana: Variazione I (largo)',
                    'movement'      => null,
                    'artists'       => 'Andrea Bacchetti',
                    'duration'      => '0:02:22',
                ),
            ),
            'text'  => 'This release is a delight from beginning to end...I greatly enjoy the character Bacchetti gives to [Capriccio sulla lontanaza del suo fratello diletissimo]...I like the sense of urbane bonhomie which Bacchetti find in these [Italian] variations, keeping our spirits up with sparks of wit amongst the sensitive regret and nostalgia elsewhere.',
            'img'   => 'bacchetti_the_italian_bach.jpg',
            'page'  => 1
        ),
        array('title' => 'An evening with the orchestra',
            'label' => 'FaLaUt',
            'year' => '2003-1-1',
            'discTracks' => array(
                array('composer'    => 'Jaques Iber',
                    'title'         => 'Flute concerto',
                    'movement'      => '1',
                    'artists'       => 'Davide Formisano',
                    'duration'      => '0:04:47',
                    'audio'         => 'formisano_evening_1.mp3'
                ),
                array('composer'    => 'Jaques Iber',
                    'title'         => 'Flute concerto',
                    'movement'      => '2',
                    'artists'       => 'Davide Formisano',
                    'duration'      => '0:07:09',
                    'audio'         => 'formisano_evening_2.mp3'
                ),
                array('composer'    => 'Jaques Iber',
                    'title'         => 'Flute concerto',
                    'movement'      => '3',
                    'artists'       => 'Davide Formisano',
                    'duration'      => '0:09:27',
                    'audio'         => 'formisano_evening_3.mp3'
                ),
            ),
            'text'  => '',
            'img'   => 'formisano_evening.jpg',
            'user'  => 11
        ),
        array('title' => 'Live in Luzern',
            'label' => 'THYM',
            'year' => '1999-1-1',
            'discTracks' => array(
                array('composer'    => 'L. van Beethoven',
                    'title'         => 'Serenade D-Dur op.8',
                    'movement'      => '',
                    'artists'       => 'Davide Formisano',
                    'duration'      => '0:16:16'
                ),
                array('composer'    => 'A. Jolivet',
                    'title'         => 'Chant de Linos',
                    'movement'      => 'Tranerlamentation',
                    'artists'       => 'Davide Formisano',
                    'duration'      => '0:11:09'
                ),
                array('composer'    => 'J. Mouquet',
                    'title'         => 'Sonate op. 15 "La flute de Pan"',
                    'movement'      => '',
                    'artists'       => 'Davide Formisano',
                    'duration'      => '0:15:02'
                ),
            ),
            'text'  => '',
            'img'   => 'formisano_luzern.jpg',
            'user'  => 11
        ),
        array('title' => 'Trio Johannes',
            'label' => 'Musicainsieme',
            'year' => '2005-1-1',
            'discTracks' => array(
                array('composer'    => 'Dimitrij Sostakovic',
                    'title'         => 'Trio in mi minore op. 67',
                    'movement'      => '1',
                    'artists'       => 'Francesca Manara violino, Massimo Polidori violoncello, Claudio Voghera pianoforte, Fabrizio Meloni clarinetto',
                    'duration'      => '0:07:29'
                ),
                array('composer'    => 'Dimitrij Sostakovic',
                    'title'         => 'Trio in mi minore op. 67',
                    'movement'      => '2',
                    'artists'       => 'Francesca Manara violino, Massimo Polidori violoncello, Claudio Voghera pianoforte, Fabrizio Meloni clarinetto',
                    'duration'      => '0:03:21'
                ),
                array('composer'    => 'Dimitrij Sostakovic',
                    'title'         => 'Trio in mi minore op. 67',
                    'movement'      => '3',
                    'artists'       => 'Francesca Manara violino, Massimo Polidori violoncello, Claudio Voghera pianoforte, Fabrizio Meloni clarinetto',
                    'duration'      => '0:05:12'
                ),
            ),
            'text'  => '',
            'img'   => 'trio_johannes.jpg',
            'user'  => 12
        ),
        array('title' => 'Duo Obliquo',
            'label' => 'THYM',
            'year' => '1998-1-1',
            'discTracks' => array(
                array('composer'    => 'Nino Rota',
                    'title'         => 'Sonata in re per clarinetto e pianoforte',
                    'movement'      => '1',
                    'artists'       => 'Carlo Boccadoro pianoforte, Fabrizio Meloni clarinetto',
                    'duration'      => '0:05:09'
                ),
                array('composer'    => 'Nino Rota',
                    'title'         => 'Sonata in re per clarinetto e pianoforte',
                    'movement'      => '2',
                    'artists'       => 'Carlo Boccadoro pianoforte, Fabrizio Meloni clarinetto',
                    'duration'      => '0:04:17'
                ),
                array('composer'    => 'Nino Rota',
                    'title'         => 'Sonata in re per clarinetto e pianoforte',
                    'movement'      => '3',
                    'artists'       => 'Carlo Boccadoro pianoforte, Fabrizio Meloni clarinetto',
                    'duration'      => '0:04:04'
                ),
            ),
            'text'  => '',
            'img'   => 'duo_obliquo.jpg',
            'user'  => 12
        ),
        array('title' => 'I fiati all\'Opera',
            'label' => 'DAD Records',
            'year' => '2002-1-1',
            'discTracks' => array(
                array('composer'    => 'Amilcare Ponchielli',
                    'title'         => 'Quartetto',
                    'movement'      => '',
                    'artists'       => 'I solisti della Scala - Philip Moll',
                    'duration'      => '0:14:07'
                ),
                array('composer'    => 'Polibio Fumagalli',
                    'title'         => 'Gran terzetto di concerto op. 40',
                    'movement'      => '',
                    'artists'       => 'I solisti della Scala - Philip Moll',
                    'duration'      => '0:11:44'
                ),
                array('composer'    => 'Benedetto Carulli',
                    'title'         => 'Duetto su "il Poliuto" di G. Donizzetti',
                    'movement'      => '',
                    'artists'       => 'I solisti della Scala - Philip Moll',
                    'duration'      => '0:06:45'
                ),
            ),
            'text'  => '',
            'img'   => 'fiati_opera.jpg',
            'user'  => 1
        ),
    );

    public static function count()
    {
        return count(DiscFixtures::$discs);
    } 

    /**
     * {@inheritDoc}
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(AbstractFixture $fixture, ObjectManager $manager, $i)
    {
        $disc = new Disc;
        $disc->setLabel(DiscFixtures::$discs[$i]['label'])
            ->setDate(new \DateTime(DiscFixtures::$discs[$i]['year']))
            ->setTitle(DiscFixtures::$discs[$i]['title'])
            ->setText(DiscFixtures::$discs[$i]['text']);

        $disc->mergeNewTranslations();
        
        /* Category */
        $category = $manager->merge($fixture->getReference('disc_category-1'));
        $disc->setCategory($category);
           
        /* Tracks */
        for ($j = 0; $j < count(DiscFixtures::$discs[$i]['discTracks']); $j++) {
            $discTrack = new DiscTrack;
            $discTrack->setNumber($j + 1)
                ->setComposer(DiscFixtures::$discs[$i]['discTracks'][$j]['composer'])
                ->setTitle(DiscFixtures::$discs[$i]['discTracks'][$j]['title'])
                ->setMovement(DiscFixtures::$discs[$i]['discTracks'][$j]['movement'])
                ->setArtists(DiscFixtures::$discs[$i]['discTracks'][$j]['artists'])
                ->setDuration(new \DateTime(DiscFixtures::$discs[$i]['discTracks'][$j]['duration']));
            if (isset(DiscFixtures::$discs[$i]['discTracks'][$j]['audio'])) {
                $discTrack->setAudio(DiscFixtures::$discs[$i]['discTracks'][$j]['audio']);
            }

            $disc->addDiscTrack($discTrack);
        }

        /* Post */
        $page = null;
        if (array_key_exists('page', DiscFixtures::$discs[$i])) {
            $page = $manager->merge($fixture->getReference('page-'.DiscFixtures::$discs[$i]['page']));
            $user = $page->getCreator();
        } elseif (array_key_exists('user', DiscFixtures::$discs[$i])) {
            $user = $manager->merge($fixture->getReference('user-'.DiscFixtures::$discs[$i]['user']));
        }

        $post = $this->container->get('cm.post_center')->getNewPost($user, $user);
        $manager->persist($post);
        $post->setPage($page);

        $disc->setPost($post);
        
        /* Protagonists */
        $userTags = array();
        for ($j = 1; $j < rand(1, 3); $j++) {
            $userTags[] = $manager->merge($fixture->getReference('user_tag-'.rand(1, 10)));
        }
        
        $disc->addUser(
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

            $disc->addUser(
                $otherUser,
                !rand(0, 3), // admin
                EntityUser::STATUS_ACTIVE,
                true, // notification
                $userTags
            );
        }
        
        /* Main Image */
        $image = new Image;
        $image->setImg(DiscFixtures::$discs[$i]['img'])
            ->setText('main image for disc "'.$disc->getTitle().'"')
            ->setMain(true)
            ->setUser($user);
        $disc->setImage($image);

        $manager->persist($disc);
        
        if ($i % 10 == 9) {
            $manager->flush();
        }

        $fixture->addReference('disc-'.($i + 1), $disc);
    }
}