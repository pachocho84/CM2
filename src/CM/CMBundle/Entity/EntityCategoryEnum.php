<?php

namespace CM\CMBundle\Entity;

class EntityCategoryEnum {
	const ENTITY	 = 0;
	const EVENT		 = 1;
	const DISC		 = 2;
	const ARTICLE	 = 3;
	const LINK		 = 4;
	const IMAGE		 = 5;
	const MULTIMEDIA = 6;
	
	static function toString($num)
	{
		switch ($num) {
			default:			   return '';
			case self::ENTITY:	   return 'Entity';
			case self::EVENT:	   return 'Event';
			case self::DISC:	   return 'Disc';
			case self::ARTICLE:	   return 'Article';
			case self::LINK:	   return 'Link';
			case self::IMAGE:	   return 'Image';
			case self::MULTIMEDIA: return 'Multimedia';
		}
	}
}