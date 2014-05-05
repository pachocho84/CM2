<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\Page;
use CM\CMBundle\Entity\PageUser;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Biography;

class PageFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private static $pages = array(
        array('name' => 'Sony Classical Italia',
            'tags' => array(26),
            'creator' => 6,
            'users' => array(
                array(
                    'user' => 6, 
                    'admin' => true,
                    'tags' => array(43)
                ),
                array(
                    'user' => 1, 
                    'admin' => true,
                    'tags' => array(41)
                ),
                array(
                    'user' => 3, 
                    'admin' => false,
                    'tags' => array(44)
                )
            ),
            'website' => 'www.sony.it',
            'img' => 'sony_classical.jpg',
            'imgOffset' => null,
            'cover' => 'sony_classical_cover.jpg',
            'coverOffset' => 34.16,
            'background' => '',
            'vip' => true,
            'biography' => "Le origini della Sony Classical risalgono al 1927, anno in cui è stata fondata la Columbia Masterworks, una etichetta discografica americana sussidiaria della Columbia Records che, nel corso degli anni, ha realizzato dischi di importanti artisti quali Isaac Stern, Pablo Casals, Vladimir Horowitz, Eugene Ormandy, Vangelis, Elliot Goldenthal e Leonard Bernstein.
Nel 1980 l'etichetta Columbia Masterworks è stata rinominata CBS Masterworks.
Successivamente, nel 1990, dopo l'acquisizione della CBS Records da parte di Sony Music, è stata infine chiamata Sony Classical (il logo utilizzato con le note magiche riecheggia il logo utilizzato dalla Columbia fino al 1955).
Il nome Masterworks continua ad essere utilizzato per la divisione classica della Sony Music Entertainment: la Sony Masterworks.
All'interno della Sony Classical è stata prodotta una serie, la Vivarte, dedicata esclusivamente alla musica eseguita su strumenti d'epoca. Tra gli artisti presenti in questa collana troviamo il clavicembalista Bob van Asperen, il violoncellista Anner Bylsma, il liutista Lutz Kirchhof, lo Huelgas Ensemble diretto da Paul Van Nevel, l'ensemble Tafelmusik, il complesso Musica Fiata diretto da Ronald Wilson e il complesso da camera L'Archibudelli.
La Sony Classical ha inoltre ripubblicato una serie di dischi della etichetta Seon fondata e prodotta da Wolf Erichson negli anni 1970-1980. Tra gli artisti presenti in catalogo troviamo la Capella Antiqua di Monaco diretta da Konrad Ruhland, Frans Brüggen, Gustav Leonhardt, Sigiswald Kuijken e Wieland Kuijken[1]. Tra i dischi ricordiamo la preziosa incisione del 1977 dei Concerti Brandeburghesi di Bach con il Leonhardt Ensemble, una delle prime incisioni su strumenti originali di quest'opera."
        ),
        array('name' => 'Società del Quartetto di Milano',
            'tags' => array(14, 24),
            'creator' => 7,
            'users' => array(
                array(
                    'user' => 7, 
                    'admin' => true,
                    'tags' => array(43)
                ),
                array(
                    'user' => 1, 
                    'admin' => true,
                    'tags' => array(41)
                ),
                array(
                    'user' => 3, 
                    'admin' => false,
                    'tags' => array(44)
                )
            ),
            'website' => 'www.quartettomilano.it',
            'img' => 'societa_quartetto.jpg',
            'imgOffset' => null,
            'cover' => 'societa_quartetto_cover.jpg',
            'coverOffset' => null,
            'background' => '',
            'vip' => true,
            'biography' => 'La Società del Quartetto di Milano è fra le associazioni concertistiche italiane quella che vanta la più lunga programmazione ininterrotta.
Ha ospitato nella sua lunga storia i protagonisti della musica, da Hans von Bülow (primo ospite stabile di fama internazionale già nel 1870 con quattro concerti, quale pianista e direttore dei primi «esperimenti sinfonici») a Richard Strauss al suo debutto italiano, da Toscanini a Claudio Abbado, da Anton Rubinstein a Busoni a Rachmaninov a Schnabel, sino a Pollini, Brendel e Radu Lupu, Schiff; da Sarasate a Hubermann, Heifetz, sino a Milstein, Repin, Mullova; dal Quartetto Joachim ai Quartetti Busch e Budapest, sino al Quartetto Italiano, al Tokyo, al Cremona in residence; dal Trio Cortot-Thibaud-Casals a Fischer-Kulenkampff-Mainardi al Trio di Trieste; da Lotte Lehmann e Elisabeth Schumann a Elizabeth Schwarzkopf e Fischer-Dieskau; da Pablo Casals a Wanda Landowska, da Schönberg a Hindemith a Stravinskij, Berio, Vacchi.
Innumerevoli "prime" ne hanno costellato il cammino: nei suoi annali le storiche prime esecuzioni italiane della Passione secondo Matteo di Bach, il 18 aprile 1878 la Nona sinfonia di Beethoven, in un memorabile concerto diretto da Franco Faccio. È il momento culminante del grande progetto di esecuzione integrale delle sinfonie di Beethoven iniziato nel 1867 con la Pastorale. Altro scopo sociale era la "fondazione di premi per concorsi": e, a partire dai concorsi di composizione originari, il Quartetto ha tenuto viva sino ad oggi la tradizione di commissionare opere nuove ai compositori contemporanei.
Tra le iniziative di risonanza internazionale si ricordano, in collaborazione col Comune di Milano, il monumentale progetto di esecuzione integrale delle Cantate di Bach iniziato nel 1994 e concluso nel novembre del 2004; i grandi oratori e capolavori della musica sacra (l’Elias, il Vespro della Beata Vergine, La Creazione); in coproduzione col Teatro alla Scala, l’integrale dei quartetti (Quartetto di Tokyo) e dei concerti per pianoforte (Alfred Brendel) di Beethoven; il ciclo dei “Grandi pianisti alla Scala” (Schiff, Brendel, Perahia, Lupu) e la presentazione delle massime orchestre mondiali dirette dai più prestigiosi direttori in attività. Basterà ricordare, i Berliner Philharmoniker diretti da Claudio Abbado nel 1993 - unico concerto milanese del periodo della sua direzione musicale della compagine tedesca - e da Simon Rattle nel 2005.'
        ),
        array('name' => 'laVerdi',
            'tags' => array(17, 24),
            'creator' => 8,
            'users' => array(
                array(
                    'user' => 8, 
                    'admin' => true,
                    'tags' => array(43)
                ),
                array(
                    'user' => 1, 
                    'admin' => false,
                    'tags' => array(41)
                )
            ),
            'website' => 'www.laverdi.org',
            'img' => 'la_verdi.jpg',
            'imgOffset' => null,
            'cover' => 'laverdi_locatelli_vivaldi.jpg',
            'coverOffset' => 17,
            'background' => '',
            'vip' => false,
            'biography' => "C'è a Milano un'orchestra sinfonica la quale non ha fatto che crescere di livello negli anni sì da divenire una grande orchestra. E' questa l'Orchestra Sinfonica di Milano Giuseppe Verdi.
Con queste parole, nel settembre scorso, l'autorevole critico musicale Paolo Isotta definiva sulle pagine del Corriere della Sera l'Orchestra Sinfonica di Milano Giuseppe Verdi, sintetizzandone chiaramente il cammino di crescita.
laVerdi, fondata nel 1993 da Vladimir Delman, si è imposta da anni come una delle più rilevanti realtà  sinfoniche nazionali, in grado di affrontare un repertorio che spazia da Bach ai capisaldi del sinfonismo ottocentesco fino alla musica del Novecento. Il cartellone della stagione 2012-13 la ventesima prevede 38  programmi sinfonici da settembre a giugno, con un'impaginazione in cui i classici sono affiancati da pagine meno consuete. Dal 2012 alla stagione principale si è affiancata una stagione estiva (luglio e agosto).
Dal 1999 al 2005 Riccardo Chailly, oggi Direttore Onorario, ha ricoperto la carica di Direttore Musicale, ruolo che dalla stagione 2009-10 è della Signora Zhang Xian (Cina). Ruben Jais, Direttore Artistico de laVerdi, e Direttore Residente. Dalla Stagione 2011-12 l'americano John Axelrod è Direttore Principale.
Il 6 ottobre 1999 è stata inaugurata - con l'esecuzione della Sinfonia n. 2 Resurrezione di Mahler, diretta da Riccardo Chailly - la nuova sede stabile dell'Orchestra, l'Auditorium di Milano Fondazione Cariplo, che per le sue caratteristiche estetiche, tecnologiche e acustiche è considerata una della migliori sale da concerto italiane.
Altro elemento distintivo dell'Orchestra è la costituzione, nell'ottobre 1998, del Coro Sinfonico di Milano Giuseppe Verdi, guidato sino alla sua scomparsa dal Maestro Romano Gandolfi, prestigiosa figura della direzione corale che ha lavorato con i più grandi direttori d'orchestra e nei più importanti teatri lirici del mondo. Il Coro conta attualmente 100 elementi in grado di affrontare il grande repertorio lirico-sinfonico dal Barocco al Novecento. Attualmente l'incarico di Maestro del Coro è ricoperto da Erina Gambarini.
Alcuni concerti ricorrenti scandiscono ogni anno il percorso musicale della Verdi: tra i quali l'appuntamento con una delle grandi Passioni di Bach in prossimità  delle festività  pasquali, il concerto di capodanno con la Nona Sinfonia di Beethoven e la Messa da requiem di Verdi.
L'Orchestra è stata diretta, tra gli altri, da Riccardo Chailly, Georges Prêtre, Riccardo Muti, Valery Gergiev, Rudolf Barshai, Claus Peter Flor, Christopher Hogwood, Helmuth Rilling, Peter Maag, Marko Letonja, Daniele Gatti, Roberto Abbado, Ivor Bolton, Kazushi Ono, Vladimir Jurowski, Yakov Kreizberg, Ulf Schirmer, Eiji Oue; inoltre ricordiamo Herbert Blomstedt, Krzysztof Penderecki, Leonard Slatkin, Vladimir Fedoseyev, Wayne Marshall, Sir Neville Marriner, Oleg Caetani, Martin Haselböck, Alan Buribayev, Aldo Ceccato, Giuseppe Grazioli, Ion Marin, Juanjo Mena, Paul Daniel."
        ),
    );

    public static function count()
    {
        return count(PageFixtures::$pages);
    } 

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {
        foreach (PageFixtures::$pages as $i => $p) {
            $user = $manager->merge($this->getReference('user-'.$p['creator']));
            $page = new Page;
            $page->setName($p['name'])
                ->setCreator($user)
                ->setWebsite($p['website'])
                ->setImg($p['img'])
                ->setImgOffset($p['imgOffset'])
                ->setCoverImg($p['cover'])
                ->setCoverImgOffset($p['coverOffset'])
                ->setVip($p['vip'])
                ->setBackgroundImg($p['background']);

            foreach ($p['tags'] as $order => $tag) {
                $page->addTag($manager->merge($this->getReference('tag-'.$tag)), $order);
            }

            $post = $this->container->get('cm.post_center')->getNewPost($user, $user, Post::TYPE_CREATION, $page->className());
            $page->addPost($post);

            $biography = new Biography;
            $biography->setTitle('b')
                ->setText($p['biography']);

            $bioPost = $this->container->get('cm.post_center')->getNewPost($user, $user);
            $bioPost->setPage($page);
            $biography->setPost($bioPost);

            $page->setBiography($biography);
            
            $manager->persist($page);
            
            /* Users */
            foreach ($p['users'] as $pUser) {
                $tags = array();
                foreach ($pUser['tags'] as $tag) {
                    $tags[] = $manager->merge($this->getReference('tag-'.$tag));
                }
                $page->addUser(
                    $manager->merge($this->getReference('user-'.$pUser['user'])),
                    $pUser['admin'], // admin
                    PageUser::STATUS_ACTIVE,
                    rand(0, 2), // join event
                    rand(0, 2), // join disc
                    rand(0, 2), // join article
                    rand(0, 1), // notification
                    $tags
                );
            }

/*
            $numbers = range(1, UserFixtures::count());
            unset($numbers[$p['creator'] - 1]);
            shuffle($numbers);
            for ($j = 0; $j < rand(0, 7); $j++) {
                $otherUser = $manager->merge($this->getReference('user-'.$numbers[$j]));
                
                $tags = array();
                for ($k = 1; $k < rand(1, 3); $k++) {
                    $tags[] = $manager->merge($this->getReference('tag-'.rand(1, 10)));
                }

                $page->addUser(
                    $otherUser,
                    false, // admin
                    PageUser::STATUS_PENDING,
                    rand(0, 2), // join event
                    rand(0, 2), // join disc
                    rand(0, 2), // join article
                    rand(0, 1), // notification
                    $tags
                );
            }
*/
            
            $this->addReference('page-'.($i + 1), $page);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        return 40;
    }
}