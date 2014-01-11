<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository as BaseRepository;

/**
 * HomepageColumnRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class HomepageColumnRepository extends BaseRepository
{
    static protected function getOptions(array $options = array())
    {
        return array_merge(array(
            'locale'        => 'en',
            'locales'       => array_values(array_merge(array('en' => 'en'), array($options['locale'] => $options['locale']))),
        ), $options);
    }
    
    public function getColumns($options)
    {
        $options = self::getOptions($options);

        return $this->createQueryBuilder('c')
            ->select('c, a, ac, act, aa, aap, aapu')
            ->leftJoin('c.archive', 'a')
            ->leftJoin('a.category', 'ac')
            ->leftJoin('ac.translations', 'act')
            ->andWhere('act.locale in (:locales)')->setParameter('locales', $options['locales'])
            ->leftJoin('a.article', 'aa')
            ->innerJoin('aa.posts', 'aap', 'with', 'aap.type = '.Post::TYPE_CREATION)
            ->andWhere('aap.object = \'CM\CMBundle\Entity\Article\'')
            ->leftJoin('aap.user', 'aapu')
            ->orderBy('c.order')
            ->getQuery()->getResult();

    // return HomepageColumnsQuery::create()->
    //         leftJoinWithHomepageArchive()->
    //         useHomepageArchiveQuery()->  
    //             leftJoinWithHomepageCategory()->
    //             leftJoinWithStampa()->                      
    //             useStampaQuery(null, 'LEFT JOIN')->  
    //         joinWithI18n(sfContext::getInstance()->getUser()->getCulture(), Criteria::LEFT_JOIN)-> 
    //               leftJoinWithsfGuardUser()->
    //                 leftJoinWithUser()->
    //                 withColumn('(SELECT COUNT(*) FROM commenti WHERE commenti.Oggetto = \'stampa\' AND commenti.Oggetto_id = stampa.Id)', 'nbComments')->  
    //             endUse()->      
    //         endUse()->
    //         leftJoinWithHomepageBox()->
    //         useHomepageBoxQuery()->                                  
    //             leftJoinWith('HomepageBox.HomepageCategory BoxCategory')-> 
    //             leftJoinWith('BoxCategory.Utenti Curatore')->                                     
    //             leftJoinWithPage()->   
    //         endUse()->              
    //   orderByOrder()->
    //   find();
    }
}
