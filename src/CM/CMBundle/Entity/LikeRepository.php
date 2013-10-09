<?php

namespace CM\CMBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * LikeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class LikeRepository extends EntityRepository
{
	public function checkIfILikeIt($type, $id)
	{
		return $this->createQueryBuilder('l')
			->select('COUNT(l)')
			->where('l.'.$type.' = :id')->setParameter('id', $id)
			->getQuery()
			->getSingleScalarResult();
	}
}
	