<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Post;
use CM\CMBundle\Entity\Biography;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private static $people = array(
        array('firstname' => 'Ernesto', /* 1 */
            'lastname' => 'Casareto',
            'username' => 'pachocho',
            'password' => 'pacho',
            'email' => 'ernesto@circuitomusica.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'city_birth' => 'Padua',
            'city_birth_coords' => array('lat' => 45.41519, 'lng' => 11.88181),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1984, 6, 17),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => 'ernesto_casareto.jpg',
            'imgOffset' => 0,
            'cover' => 'ernesto_casareto_cover.jpg',
            'coverOffset' => 10,
            'tags' => array(5, 39),
            'biography' => 'Ernesto Casareto, flautista, si è diplomato col massimo dei voti al Conservatorio “Giuseppe Verdi” di Milano.
Si è poi perfezionato con Emilio Vapi prima all’Accademia G. Marziali di Seveso e poi all’Accademia Internazionale della Musica di Milano.
Dopo aver conseguito la maturità presso il liceo musicale annesso al Conservatorio ha studiato Scienze e Tecnologie della Comunicazione Musicale presso l’Università degli Studi di Milano e si è poi laureato in Psicologia con indirizzo Comunicazione & Marketing presso l’Università Cattolica del Sacro Cuore di Milano.
Sin dai primi anni ha partecipato con successo a numerosi concorsi, tra cui primo premio assoluto al concorso Rotary International e la borsa di studio al concorso "Severino Gazzelloni".
È uno dei pochissimi flautisti italiani finalisti per l’orchestra EUYO.
Alla grande passione per l’attività cameristica e didattica affianca la collaborazione con diverse orchestre che negli anni lo hanno portato ad esibirsi in alcune delle più prestigiose sale da concerto europee, americane e asiatiche.
Suona un flauto Yamaha 18 carati all gold.
È fondatore e Project Manager di Circuito Musica, il più moderno ed efficiente portale dedicato alla musica classica che affianca ad uno strumento di promozione per gli artisti un rivoluzionario polo di diffusione culturale.'
        ),
        array('firstname' => 'Fabrizio',  /* 2 */
            'lastname' => 'Castellarin',
            'username' => 'EnoahNetzach',
            'password' => 'pacho',
            'email' => 'f.castellarin@gmail.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_STUDENT,
            'vip' => false,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1990, 4, 19),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => 'fabrizio_castellarin.jpg',
            'imgOffset' => null,
            'cover' => 'fabrizio_castellarin_cover.jpg',
            'coverOffset' => null,
            'tags' => array(4),
            'biography' => null
        ),
        array('firstname' => 'Federica', /* 3 */
            'lastname' => 'Fontana',
            'username' => 'federica_fontana',
            'password' => 'pacho',
            'email' => 'f.fontana@gmail.it',
            'sex' => User::SEX_F,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'city_birth' => 'Taranto',
            'city_birth_coords' => array('lat' => 40.47611, 'lng' => 17.22972),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1900, 4, 15),
            'birth_date_visible' => User::BIRTHDATE_NO_YEAR,
            'img' => 'federica_fontana.jpg',
            'imgOffset' => null,
            'cover' => 'federica_fontana_cover.jpg',
            'coverOffset' => null,
            'tags' => array(1),
            'biography' => 'Federica Fontana, Avvocato e Responsabile partner e sponsor di Circuito Musica. Diplomata in pianoforte con il massimo dei voti e la lode presso il Conservatorio “Duni” di Matera e laureata in Giurisprudenza all’Università degli Studi di Bari con tesi in Filosofia del diritto "Paradigmi sinfonici della scienza giuridica", ottenendo la votazione di 110 e lode. Ha completato il suo percorso formativo conseguendo il Master SIS in Spettacolo Impresa e Società presso l\'Università degli Studi di Milano Bicocca. Una pianista-giurista, come ama definirsi, che mette a disposizione di Circuito Musica le sue competenze musicali, giuridiche e organizzative in ambito musicale maturate anche durante l\'esperienza nella Produzione del Festival MITO Settembremusica. Scrive e registra recensioni video per Sky Classica, il canale di Sky dedicato interamente alla musica classica.'
        ),
        array('firstname' => 'Luca', /* 4 */
            'lastname' => 'Casareto',
            'username' => 'luca_casareto',
            'password' => 'pacho',
            'email' => 'lucasareto@gmail.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1990, 5, 15),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => 'luca_casareto.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Virginia', /* 5 */
            'lastname' => 'Nolte',
            'username' => 'cinnamoon',
            'password' => 'pacho',
            'email' => 'vir@circuitomusica.it',
            'sex' => User::SEX_F,
            'type' => User::TYPE_KEEN,
            'vip' => false,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1994, 3, 29),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'virginia_nolte.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Mario', /* 6 */
            'lastname' => 'Marcarini',
            'username' => 'mario_marcarini',
            'password' => 'pacho',
            'email' => 'mamarcarini@circuitomusica.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1971, 3, 21),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => 'mario_marcarini.jpg',
            'imgOffset' => null,
            'cover' => 'mario_marcarini_cover.jpg',
            'coverOffset' => null,
            'tags' => array(10, 9, 11),
            'biography' => 'Nato a Milano in un\'era geologica imprecisata, dedica in gioventù inutili sforzi allo studio del canto, sacrificando una sicura carriera di centromediano sull\'altare di non meglio precisate velleità tenorili. Noncurante dei vaticini di chi lo consigliava vivamente di affidare alla professione di idraulico un futuro economico appagante, opta per la facoltà di lettere e filosofia, votandosi alla storia della critica d\'arte, seguendo la massima aurea che recita: "se non sai fare, critic; se non sai criticare insegna; se non sai insegnare, dirigi." Falliti dunque tutti gli stadi successivi alla critica, dedica con tenacia tutti i suoi interessi e passioni allo studio, alla scoperta ma soprattutto alla degustazione di ottimi vini. Nel tempo libero occupa indegnamente l\'incarico di Label manager (non ha ancora capito nemmeno lui che cosa voglia dire, e di conseguenza quali siano i suoi incarichi) in una nota multinazionale discografica.'
        ),
        array('firstname' => 'Dora', /* 7 */
            'lastname' => 'Alberti',
            'username' => 'dora_alberti',
            'password' => 'pacho',
            'email' => 'doralberti@circuitomusica.it',
            'sex' => User::SEX_F,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1971, 3, 12),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => 'dora_alberti.jpg',
            'imgOffset' => null,
            'cover' => 'societa_quartetto_cover.jpg',
            'coverOffset' => null,
            'tags' => array(),
            'biography' => 'Segretaria della Società del Quartetto, si occupa della biglietteria e dei rapporti con i Soci.'
        ),
        array('firstname' => 'Francesca', /* 8 */
            'lastname' => 'Cremonini',
            'username' => 'francesca_cremonini',
            'password' => 'pacho',
            'email' => 'fracremonini@circuitomusica.it',
            'sex' => User::SEX_F,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1964, 11, 17),
            'birth_date_visible' => User::BIRTHDATE_NO_YEAR,
            'img' => 'francesca_cremonini.jpg',
            'imgOffset' => null,
            'cover' => 'laverdi_locatelli_vivaldi.jpg',
            'coverOffset' => null,
            'tags' => array(),
            'biography' => 'Responsabile marketing laVerdi Milano.'
        ),
        array('firstname' => 'Luca', /* 9 */
            'lastname' => 'Di Giulio',
            'username' => 'luca_digiulio',
            'password' => 'luca',
            'email' => 'lucadigiulio@circuitomusica.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'writer' => true,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1983, 1, 1),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'luca_di_giulio.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'tags' => array(40),
            'biography' => 'Luca Di Giulio, milanese. Dopo la laurea in Lettere Moderne, con una tesi in Storia della Musica, frequenta il Master in Management per lo Spettacolo organizzato dalla Business School dell’Università Commerciale Luigi Bocconi in collaborazione con l’Accademia Teatro alla Scala e il Piccolo Teatro di Milano. Svolge parallelamente importanti esperienze formative presso le più rilevanti realtà del settore, tra cui la casa discografica Stradivarius e il Teatro alla Scala, con cui collabora attivamente dalla stagione 2010-2011 e da cui viene richiamato a collaborare per le stagioni 2012/2013 e 2013/2014. A partire dal 2011 è autore dei programmi di sala per il Festival Concerti d’Autunno presso la Sala Greppi di Bergamo. Come frutto ultimo di questa costante ricerca in ambito musicale, nonché dell’approfondita conoscenza del settore, decide nel 2011 di intraprendere l’attività manageriale fondando la Di Giulio Management. In pochi mesi costruisce con cura e passione un eterogeneo portfolio di artisti di levatura internazionale, comune denominatore l’interesse per giovani emergenti, tutti selezionati personalmente con grandissima attenzione.'
        ),
        array('firstname' => 'Fabio', /* 10 */
            'lastname' => 'Rizzi',
            'username' => 'fabio_rizzi',
            'password' => 'fabio',
            'email' => 'fabiorizzi@circuitomusica.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => false,
            'writer' => true,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1975, 11, 19),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'fabio_rizzi.jpg',
            'imgOffset' => null,
            'cover' => 'fabio_rizzi_cover.jpg',
            'coverOffset' => 17,
            'tags' => array(6),
            'biography' => 'Nato nel 1975, si è diplomato al Conservatorio “Giuseppe Verdi” di Milano vincendo il “Premio speciale del Direttore”. Ha conseguito i Diplomi Accademici di II° Livello ad Indirizzo Solistico ed in Musica da Camera presso il Conservatorio di Piacenza, entrambi “Summa cum Laude”.
Perfezionatosi all’Accademia del Teatro alla Scala e in Germania con Thomas Indermühle e Heinz Holliger, è stato Primo Oboe della Ferruccio Busoni Academische Orchester di Freiburg dal 1997 al 1999 e dal 1999 al 2001 ha suonato come Corno Inglese nella European Union Youth Orchestra, sotto la direzione di Vladimir Ashkenazy e Bernard Haitink.
Ha vinto una borsa di studio per frequentare il Master of Music al New England Conservatory di Boston (USA), dove è stato scelto dal M.° Seiji Ozawa tra i migliori allievi per suonare con la Boston Symphony Orchestra.
Dal 2002 al 2007 ha suonato nell’Orchestra Sinfonica di Milano “Giuseppe Verdi”, diretta dal M.° Riccardo Chailly.
Dal 2008 collabora come Corno Inglese Solista con l’orchestra del Teatro dell’Opera di Roma, diretta dal M.° Riccardo Muti. Dopo la prima dell’Attila di Verdi diretta da Muti, Il Messaggero ha commentato: “…la bravura dell’orchestra trova la sua sintesi nei soli del corno inglese Fabio Rizzi.”
È stato per diversi anni Primo Oboe nell’orchestra del Tiroler Festspiele Erl, festival lirico austriaco diretto da Gustav Kuhn, con cui ha eseguito tutte le opere di Wagner e Richard Strauss, oltre a quasi tutte le Sinfonie di Mahler, Bruckner, Brahms, Tschaikowskji e Beethoven.
Dal 2003 è membro dell’Orchestra UNIMI di Milano, una delle compagini sinfoniche giovanili più apprezzate in Europa, con cui si è esibito alla Tonhalle di Zurigo e al Gewandhaus di Lipsia.
Ha suonato con i più grandi Maestri del nostro tempo: Seiji Ozawa, James Levine, Lorin Maazel, Riccardo Muti, Claudio Abbado, Riccardo Chailly, Bernard Haitink, Charles Dutoit, Vladimir Ashkenazy, Georges Prêtre, James Conlon, Fabio Luisi, Daniele Gatti, Gustavo Dudamel, Christian Thielemann, Rafael Frübeck de Burgos, Daniel Oren e Vladimir Jurowski,
Finalista idoneo in numerosi concorsi internazionali, ha collaborato con prestigiose orchestre quali Boston Symphony Orchestra, Münchner Philharmoniker, Dresdner Philharmonie, Aalto Musiktheater Essen, Teatro alla Scala, Teatro Petruzzelli di Bari, I Pomeriggi Musicali, Orchestra Filarmonica Italiana, Orchestra da Camera di Mantova, Orchestra Nova Et Vetera, I Solisti Pavesi.
Esibitosi come solista in Italia e all’estero, è stato più volte invitato negli USA alle Conference della International Double Reed Society, come testimonial dell’atelier Lorée De Gourdon.
È socio fondatore di Circuito Musica.'
        ),
        array('firstname' => 'Davide', /* 11 */
            'lastname' => 'Formisano',
            'username' => 'davide_formisano',
            'password' => 'pacho',
            'email' => 'davideformisano@circuitomusica.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => true,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1974, 11, 19),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'davide_formisano.jpg',
            'imgOffset' => null,
            'cover' => 'davide_formisano_cover.jpg',
            'coverOffset' => null,
            'tags' => array(5),
            'biography' => 'Davide Formisano è nato nel 1974 a Milano, dove si è diplomato col massimo dei voti e la lode sotto la guida del M° Carlo Tabarelli, perfezionandosi in seguito con i maestri Glauco Cambursano,Bruno Cavallo, Jean Claude Gerard presso la Musikhochschule di Stoccarda ed Aurele Nicolet a Basilea,Il quale influenzera\' fortemente il suo gusto musicale.
A diciassette anni si pone all\'attenzione di Sir James Galway e Patrick Gallois,anche loro fondamentali nella sua formazione flautistica e musicale.
Giovanissimo, si aggiudica il Primo Premio al Concorso “G. Galilei” di Firenze ed al Concorso Internazionale di Stresa, ottenendo successivamente prestigiosi riconoscimenti presso tutti i più autorevoli concorsi internazionali. Diciassettenne, si presenta al IV Concorso Jean-Pierre Rampal di Parigi e consegue il Prix Special du Jury, ottenendo negli anni seguenti il Primo Premio al Concorso Internazionale di Budapest ed il Secondo Premio, con primo non assegnato, al concorso ARD di Monaco di Baviera.
Primo flautista italiano ad aver ottenuto tali riconoscimenti, Davide Formisano aveva già suonato giovanissimo con le più importanti compagini giovanili europee, come l’Orchestra Giovanile Italiana, lo Schleswig-Holstein Festival Orchester e la European Community Youth Orchestra, diretta da maestri del calibro di Lorin Maazel, Carlo Maria Giulini e Kurt Sanderling.
Nel 1995 ottiene il posto di Flauto solista nella Filarmonisches Staatorchester di Amburgo, ricoprendo nel 1996 lo stesso ruolo presso la Netherlands Radio Philarmonic Orchestra. Dal marzo 1997 al luglio 2012 è Primo Flauto Solista dell’Orchestra del Teatro alla Scala e dell’omonima Filarmonica. L’attività in seno all’Orchestra Filarmonica della Scala gli ha permesso di collaborare con direttori di fama mondiale, quali Carlo Maria Giulini, Zubin Mehta, Wolfgang Savallisch, Valery Gergev, Myung wun Chung, George Pretre e Giuseppe Sinopoli, Riccardo Muti,Gustavo Dudamel,Daniel Harding, Daniel Baremboim, Daniele Gatti e Riccardo Chailly.
Nel luglio 2012 decide di lasciare tale posizione per poter dedicarsi a pieno alla sua attivita\' cameristica , Solistitica e didattica.
Davide Formisano ha accostato al ruolo di professore d’orchestra una brillante e crescente carriera cameristica e solistica, esibendosi in Europa,Asia,Nord America ed America Latina con partners del calibro di Bruno Canino, Radovan Vlatkovic, Phillipp Moll, Sergio Azzolini, Fabio Biondi,Daniel Barenboim,Riccardo Muti,James Galway,Philippe Entremont,Thomas Sanderling e accompagnato da orchestre quali Bayerischer Rundfunk, Dresdner Kapellsolisten,Orchestra Filarmonica della Scala, Filarmonica di S. Pietroburgo e Tonhalle Ensemble di Zurigo,I Cameristi della Scala,Basel Rundfunk Orchester,Badische Staatskappelle.
Accolto sempre con grande successo dal pubblico, la stampa italiana e tedesca si è così espressa , in occasione di alcuni concerti con l’ensemble Dresdner Kappelsolisten:- "Un flauto magico strega l\'Auditorium" (Il Messaggero, Roma); "... un solista italiano, ospite del gruppo... che ha letteralmente trascinato i suoi partners con uno slancio ed una felicità interpretative fuori dal comune, esibendo un suono meraviglioso ed una proprietà stilistica indiscutibile…” (La Repubblica, Firenze), “Semplice gioia nel suonare” (Frankfurt Allegemeine), "... perchè non ha solo precisione, magnifico controllo del fiato e un suono sempre sicuro e rotondo... è un musicista che sa dare espressione a tutto... introdotti da Formisano come un\'illuminazione improvvisa e felice" (la Stampa).
Nel 1998 e 1999 partecipa ai Festivals di Lucerna, St. Moritz, Basilea, Neuchátel, Rheingau e Bad Wörishofen. In Italia ha suonato a Siena con il pianista Phillip Moll, a Milano per le “Serate Musicali” e a Firenze per gli “Amici della Musica” con Bruno Canino, e a Ravenna con alcuni Solisti dei “Wiener Philarmoniker” ed il M° Riccardo Muti al pianoforte. Dal 2000 al 2002, Davide Formisano è stato ospite in numerosi festival in Svizzera (Baden ed a Lucerna) e in Germania (Würzburg, Neumarkt e Rügen).
Nel 2003 esegue Il concerto di Jaques Ibert con l\' orchestra della radio di Basilea dal quale realizzera\' un cd live.
Nel 2004 si esibisce da solista con l\'Orchestra Filarmonica della Scala durante una tournè mondiale.
Nel 2007 vince il concorso presso la prestigiosa Hochschule fuer musik di Stoccarda in Germania diventandone "Haupt professor".
Nel 2009,in occasione della convention americana a New York,si esibisce nella serata inaugurale davanti a 3000 persone che entusiaste lo ringrazieranno con una standing ovation.
Nel 2010 suona il concerto di Mozart in re nella prestigiosa "Goldener saal" di Vienna.
Il 2011 lo vede impegnato in numerosi concerti e masterclass:ad Istanbul  esegue il concerto in mi minore di Mercadante ed  a Caracas, primo flautista italiano invitato dal famoso "Sistema Venezuelano" riscuote un enorme successo accompagnato dall\' orchestra "Simon Bolivar".
La Badische Staatskappelle lo invita nel maggio 2011 ad eseguire il concerto di Mozart riscuotendo un successo di pubblico e critica clamoroso.
 
E\' stato protagonista di numerose tournèe in Giappone, durante le quali si è esibito al Festival Internazione di Fuji, allo Yamanami Music Festival, fino al recente debutto alla prestigiosa sala Bunka-Kaikan di Tokyo e al Metropolitan Art Space, insieme al pianista Phillipp Moll. 
Ha già inciso l’integrale dei Quartetti di Mozart per flauto ed archi con il Quartetto Tartini, un Recital live con pianoforte in occasione del debutto alle Settimane Musicali di Lucerna, un cd su arie di opere italiane accompagnato da Phillipp Moll, in collaborazione con Sergio Azzolini e J. C. Gerard.
Davide Formisano tiene regolarmente delle Master Class presso l\'Academie d\'Etè di Nizza e presso l\'Hamamatsu Music Festival in Giappone.
Davide Formisano suona con un flauto Muramatsu 24k All Gold'
        ),
        array('firstname' => 'Fabrizio', /* 12 */
            'lastname' => 'Meloni',
            'username' => 'fabrizio_meloni',
            'password' => 'pacho',
            'email' => 'fabriziomeloni@circuitomusica.it',
            'sex' => User::SEX_M,
            'type' => User::TYPE_PROFESSIONAL,
            'vip' => true,
            'city_birth' => 'Milan',
            'city_birth_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'city_current' => 'Milan',
            'city_current_coords' => array('lat' => 45.46427, 'lng' => 9.18951),
            'birth_date' => array(1789, 11, 19),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'fabrizio_meloni.jpg',
            'imgOffset' => null,
            'cover' => 'fabrizio_meloni_cover.jpg',
            'coverOffset' => 70,
            'tags' => array(7),
            'biography' => 'Primo clarinetto solista dell’Orchestra del Teatro e della Filarmonica della Scala dal 1984, ha compiuto gli studi musicali al Conservatorio “Giuseppe Verdi” di Milano diplomandosi con il massimo dei voti, la lode e la menzione d’onore. Vincitore di concorsi nazionali e internazionali: ARD, Monaco(1986),Primavera di Praga (1986).
Ha collaborato con solisti di fama internazionale quali Bruno Canino, Alexander Lonquich, Michele Campanella, Heinrich Schiff, Friederich Gulda, Editha Gruberova, il Quartetto Hagen, Myung-Whun  Chung, Philip Moll e R. Muti D.Baremboin nella veste straordinaria di pianista.
Ha tenuto tournée negli Stati Uniti e in Israele con il Quintetto a Fiati Italiano,  eseguendo brani dedicati a questa formazione da Berio e Sciarrino
(dal 1989 al 1994 ha collaborato intensamente con Luciano Berio). Con il Nuovo Quintetto Italiano, nato nel 2003, ha già all’attivo tournée in Sud America e nel Sud Est Asiatico. La sua tournée d concerti in Giappone con Philipp Moll e I Solisti della Scala  è stata accolta da entusiastici consensi di pubblico e critica: il programma di musiche italiane è stato raccolto nel CD “I fiati all’Opera” (DAD Records). Ha all’attivo diverse incisioni discografiche: il Concerto K622 e la Sinfonia Concertante di Mozart per clarinetto e orchestra con l’Orchestra Filarmonica della Scala diretta da Riccardo Muti; “Pulcherrima Ignota” (con l’E. Bairav Ensemble, tributo alle musiche zingare nel mondo), il Duo-Obliquo (con Carlo Boccadoro, compositore, pianista e percussionista), l’Histoire du Soldat di Stavinskij nella doppia versione per trio e settimino e i Quintetti per clarinetto e archi di Mozart e Brahms , un Cd dedicato interamente agli inediti di Mercadante .

Nell’aprile 2006 è uscita per AMADEUS, la più importante rivista musicale italiana, la registrazione dei concerti per clarinetto di Mercadante, Donizetti e Rossini. Nel giugno dello stesso anno è stata pubblicata dalla stessa rivista l’incisione (definita “magistrale” ) delle due Sonate op. 120 per pianoforte e clarinetto di Brahms con Nazzareno Carusi. 

Cord Garben, direttore artistico di tutte le registrazioni discografiche di Arturo Benedetti Michelangeli per oltre quindici anni, ha così recensito il suo debutto alla Brahms-Gesellschaft di Amburgo nel novembre 2007 in duo con Nazzareno Carusi: ““I musicisti hanno brillato d’un livello tecnico incredibile e spericolato. Un insieme praticamente perfetto e una scala completa di espedienti espressivi hanno fatto dell’ascolto un’avventura indimenticabile”.

È stato invitato a tenere master class dal Conservatorio Superiore di Musica di Parigi, dal Conservatorio della Svizzera Italiana, dalla Manhattan School of Music New York, Northeastern Illinois University di Chicago, Music Academy of the West di Los Angeles e dalle Università di Tokyo e Osaka.  Docente dei corsi di alto perfezionamento Annuali: Accademia delle Arti e dei Mestieri del Teatro alla Scala, Milano Music Master , Roma:Associazione Lirico Musicale “Giovani all’opera”, Conservatorio Tomadini di Udine ,  Conservatorio Superiore di Musica di Saragozza Istituto Musicale “Angelo Masini” Cesena,Istituto Superiore  A.Peri (Reggio Emilia) Conservatorio Musica P.Tchaikovsky (CZ),MUSICARTS 

Tra gli ultimi progetti realizzati DVD Duets “Il clarinetto nel Jazz e nel 900 italiano” edito da Limen (Warner Chappel Music), l’incisione dei concerti di Jean Françaix, Carl Nielsen e Aaron Copland, progetto mai realizzato da un musicista italiano. 
Con L’etichetta  Limen Music  ha registrato cd e dvd dei quintetti per clarinetto e quartetto d’archi di W.A.Mozart J.Brahms (M.Rizzi vl, M.Baremboin vl, D.Rossi vla, A.Persichilli vlc), IN DUO con Pianista T.Yoshikawa “ACROSS VIRTUOSITY” entrambi presentati al Teatro alla Scala.'
        ),
    );

    public static function count()
    {
        return count(UserFixtures::$people);
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
        $userManager = $this->container->get('fos_user.user_manager');

        foreach (UserFixtures::$people as $i => $person) {
            $user = $userManager->createUser();
            $date = new \DateTime;
            $user->setEmail($person['email'])
               ->setUsername($person['username'])
               ->setPlainPassword($person['password'])
               ->setEnabled(true)
               ->setFirstName($person['firstname'])
               ->setLastName($person['lastname'])
               ->setSex($person['sex'])
               ->setType($person['type'])
               ->setCityBirth($person['city_birth'])
               ->setCityBirthLatitude($person['city_birth_coords']['lat'])
               ->setCityBirthLongitude($person['city_birth_coords']['lng'])
               ->setCityCurrent($person['city_current'])
               ->setCityCurrentLatitude($person['city_current_coords']['lat'])
               ->setCityCurrentLongitude($person['city_current_coords']['lng'])
               ->setBirthDate($date->setDate($person['birth_date'][0], $person['birth_date'][1], $person['birth_date'][2]))
               ->setBirthDateVisible($person['birth_date_visible'])
               ->setImg($person['img'])
               ->setImgOffset($person['imgOffset'])
               ->setCoverImg($person['cover'])
               ->setCoverImgOffset($person['coverOffset'])
               ->setVip($person['vip']);
            if (array_key_exists('writer', $person)) {
                $user->addRole('ROLE_WRITER');
            }

            // $post = $this->container->get('cm.post_center')->getNewPost($user, $user, Post::TYPE_CREATION, $user->className());

            // $user->addPost($post);

            if (array_key_exists('biography', $person)) {

                $biography = new Biography;
                $biography->setTitle('b')
                    ->setText($person['biography']);
    
                $bioPost = $this->container->get('cm.post_center')->getNewPost($user, $user);
                $biography->setPost($bioPost);
    
                $user->setBiography($biography);
                
                $manager->persist($user);
            }

            foreach ($person['tags'] as $order => $tag) {
                $user->addTag($manager->merge($this->getReference('tag-'.$tag)), $order);
            }
            
            $manager->persist($user);
            $this->addReference('user-'.($i + 1), $user);
        
            $manager->flush();
        }
	}

	public function getOrder()
	{
        return 20;
    }
}