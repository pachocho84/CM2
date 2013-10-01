<?php

namespace CM\CMBundle\Entity;

class Locale
{
	private $locale;

	public function __construct($_locale)
	{
		$this->locale = $_locale;
	}

	public function __invoke()
	{
		return $this->get();
	}

	public function __toString()
	{
		return $this->get();
	}

	public function found()
	{
		return true;
	}

	public function notFound()
	{
		return false;
	}

	public function get()
	{
		return $this->locale;
	}
}