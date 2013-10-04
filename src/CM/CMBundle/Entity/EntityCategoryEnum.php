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
	
	static function toNum($string)
	{
		switch ($string) {
			default:			  	 return -1;
			case 'Entity':		 return self::ENTITY;
			case 'Event':			 return self::EVENT;
			case 'Disc':			 return self::DISC;
			case 'Article': 	 return self::ARTICLE;
			case 'Link':			 return self::LINK;
			case 'Image':			 return self::IMAGE;
			case 'Multimedia': return self::MULTIMEDIA;
		}
	}
}