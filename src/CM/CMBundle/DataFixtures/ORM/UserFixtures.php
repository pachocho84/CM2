<?php

namespace CM\CMBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use CM\CMBundle\Entity\User;
use CM\CMBundle\Entity\Biography;
use CM\CMBundle\Entity\EntityCategory;

class UserFixtures extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $people = array(
        array('firstname' => 'Er Nesto',
            'lastname' => 'Casareto',
            'username' => 'pachocho',
            'password' => 'pacho',
            'email' => 'ernesto@circuitomusica.it',
            'vip' => true,
            'writer' => true,
            'sex' => User::SEX_M,
            'city_birth' => 'Padua',
            'city_current' => 'Milan',
            'birth_date' => array(1984, 6, 17),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => '1074169_10101332211257910_2077101884_o.jpg',
            'imgOffset' => 0,
            'cover' => 'erteda50c9566f74859fb54a7f7912225deed5f1.jpg',
            'coverOffset' => 10,
            'user_tags' => array(1, 5),
            'biography' => 'Ernesto Casareto, flautista, si è diplomato col massimo dei voti al Conservatorio “Giuseppe Verdi” di Milano.
Si è poi perfezionato con Emilio Vapi prima all’Accademia G. Marziali di Seveso e poi all’Accademia Internazionale della Musica di Milano.
Dopo aver conseguito la maturità presso il liceo musicale annesso al Conservatorio ha studiato Scienze e Tecnologie della Comunicazione Musicale presso l’Università degli Studi di Milano e si è poi laureato in Psicologia con indirizzo Comunicazione & Marketing presso l’Università Cattolica del Sacro Cuore di Milano.
Sin dai primi anni ha partecipato con successo a numerosi concorsi, tra cui primo premio assoluto al concorso Rotary International e la borsa di studio al concorso "Severino Gazzelloni".
È uno dei pochissimi flautisti italiani finalisti per l’orchestra EUYO.
Alla grande passione per l’attività cameristica e didattica affianca la collaborazione con diverse orchestre che negli anni lo hanno portato ad esibirsi in alcune delle più prestigiose sale da concerto europee, americane e asiatiche.
Suona un flauto Yamaha 18 carati all gold.
È fondatore e Project Manager di Circuito Musica, il più moderno ed efficiente portale dedicato alla musica classica che affianca ad uno strumento di promozione per gli artisti un rivoluzionario polo di diffusione culturale.'
        ),
        array('firstname' => 'Fabrizio', 
            'lastname' => 'Castellarin',
            'username' => 'EnoahNetzach',
            'password' => 'pacho',
            'email' => 'f.castellarin@gmail.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1990, 4, 19),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => '00b7b971d96ce05797e6757e5a0a4232.jpeg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(4),
            'biography' => null
        ),
        array('firstname' => 'Federica',
            'lastname' => 'Fontana',
            'username' => 'fede_pucci_pucci',
            'password' => 'pacho',
            'email' => 'f.fontana@gmail.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Taranto',
            'city_current' => 'Milan',
            'birth_date' => array(1900, 4, 15),
            'birth_date_visible' => User::BIRTHDATE_NO_YEAR,
            'img' => '664508_4284090172825_1093880282_o.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(1),
            'biography' => null
        ),
        array('firstname' => 'Luca',
            'lastname' => 'Casareto',
            'username' => 'ghey',
            'password' => 'pacho',
            'email' => 'lucasareto@gmail.it',
            'vip' => true,
            'sex' => User::SEX_M,
            'city_birth' => 'Padua',
            'city_current' => 'Milan',
            'birth_date' => array(1990, 5, 15),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => '263757_2174482600735_3488733_n.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Virginia Alexandra',
            'lastname' => 'Nolte',
            'username' => 'cinnamoon',
            'password' => 'pacho',
            'email' => 'vir@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1994, 3, 29),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => '1394182_652549711456783_1108378362_n.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Mario',
            'lastname' => 'Marcarini',
            'username' => 'mariomarcarini',
            'password' => 'pacho',
            'email' => 'mamarcarini@circuitomusica.it',
            'sex' => User::SEX_M,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1971, 3, 21),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => '8d89c5d232b971a8fbad65abe5fa21b0de7e1048.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(9, 8, 10),
            'biography' => null
        ),
        array('firstname' => 'Dora',
            'lastname' => 'Alberti',
            'username' => 'doralberti',
            'password' => 'pacho',
            'email' => 'doralberti@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1971, 3, 12),
            'birth_date_visible' => User::BIRTHDATE_VISIBLE,
            'img' => 'e9ad7f19c9c4909fb82ff647d5fb43527e4f1aad.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Francesca',
            'lastname' => 'Cremonini',
            'username' => 'fracremonini',
            'password' => 'pacho',
            'email' => 'fracremonini@circuitomusica.it',
            'sex' => User::SEX_F,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1964, 11, 17),
            'birth_date_visible' => User::BIRTHDATE_NO_YEAR,
            'img' => 'b0702dfa74da4333342e764750476cfddd4c5b12.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Luca',
            'lastname' => 'Di Giulio',
            'username' => 'luca_digiulio',
            'password' => 'luca',
            'email' => 'lucadigiulio@circuitomusica.it',
            'sex' => User::SEX_M,
            'writer' => true,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1983, 1, 1),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'f55b9c3842af704ce991a2c54c21fdf081906970.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(),
            'biography' => null
        ),
        array('firstname' => 'Fabio',
            'lastname' => 'Rizzi',
            'username' => 'fabio_rizzi',
            'password' => 'fabio',
            'email' => 'fabiorizzi@circuitomusica.it',
            'sex' => User::SEX_M,
            'writer' => true,
            'city_birth' => 'Milan',
            'city_current' => 'Milan',
            'birth_date' => array(1975, 11, 19),
            'birth_date_visible' => User::BIRTHDATE_INVISIBLE,
            'img' => 'f55b9c3842af704ce991a2c54c21fdf081906970.jpg',
            'imgOffset' => null,
            'cover' => null,
            'coverOffset' => null,
            'user_tags' => array(),
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
		  	   ->setPlainPassword($person['password'])
		  	   ->setEnabled(true)
		  	   ->setFirstName($person['firstname'])
		  	   ->setLastName($person['lastname'])
		  	   ->setSex($person['sex'])
		  	   ->setCityBirth($person['city_birth'])
		  	   ->setCityCurrent($person['city_current'])
		  	   ->setBirthDate($date->setDate($person['birth_date'][0], $person['birth_date'][1], $person['birth_date'][2]))
		  	   ->setBirthDateVisible($person['birth_date_visible'])
		  	   ->setImg($person['img']);
            if (array_key_exists('vip', $person)) {
                $user->setVip($person['vip']);
            }
            if (array_key_exists('writer', $person)) {
                $user->addRole('ROLE_WRITER');
            }
            if (array_key_exists('imgOffset', $person)) {
                $user->setImgOffset($person['imgOffset']);
            }
            if (array_key_exists('cover', $person)) {
                $user->setCoverImg($person['cover']);
            }

            
            $post = $this->container->get('cm.post_center')->getNewPost($user, $user);

            $user->addPost($post);

            if (array_key_exists('biography', $person)) {
                $biography = new Biography;
                $biography->setTitle('b')
                    ->setText($person['biography']);

                $post = $this->container->get('cm.post_center')->getNewPost($user, $user);
                $biography->addPost($post);

                $manager->persist($biography);
            }

            for ($j = 0; $j < count($person['user_tags']); $j++) {
                $userTag = $manager->merge($this->getReference('user_tag-'.$person['user_tags'][$j]));
                
                $user->addUserTag($userTag, $j);
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