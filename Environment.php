<?php

namespace pelish8\FisherMan;

class Enviroment
{
	protected static $instance = null;

	protected $defaultLogPath = 'default/Path';

	protected $ip = null;

	protected $method = null;

	protected $route = null;

	protected function __construt()
	{
	}

	public static function sharedEnviroment()
	{
		if (static::instance === null) {
			static::instance = new static();
		}

		return static::instance;
	}

	public function logPath()
	{
		return $this->defaultLogPath;
	}
}
