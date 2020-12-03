<?php

namespace Axis;

use Axis\Globals;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Log {

	protected static ?Logger $instance = null;

	private function __construct(){}

	/**
	 * Method to get the Monolog instance
	 *
	 * @return \Monolog\Logger
	 */
	public static function getLogger()
	{
		if (is_null(self::$instance)) {
			self::configureInstance();
		}

		return self::$instance;
	}

	/**
	 * Configure Monolog instance
	 */
	protected static function configureInstance()
	{
		$dir = Globals::$logs;

		if (!file_exists($dir)){
			mkdir($dir, 0777, true);
		}

		$logger = new Logger('axis',
			[
				new StreamHandler("php://stdout"),
				new RotatingFileHandler("{$dir}/all.log", 10),
				new RotatingFileHandler(
					"{$dir}/error.log", 10, Logger::ERROR)
			]
		);

		self::$instance = $logger;
	}

	public static function debug($message, array $context = []){
		self::getLogger()->debug($message, $context);
	}

	public static function info($message, array $context = []){
		self::getLogger()->info($message, $context);
	}

	public static function notice($message, array $context = []){
		self::getLogger()->notice($message, $context);
	}

	public static function warning($message, array $context = []){
		self::getLogger()->warning($message, $context);
	}

	public static function error($message, array $context = []){
		self::getLogger()->error($message, $context);
	}

	public static function critical($message, array $context = []){
		self::getLogger()->critical($message, $context);
	}

	public static function alert($message, array $context = []){
		self::getLogger()->alert($message, $context);
	}

	public static function emergency($message, array $context = []){
		self::getLogger()->emergency($message, $context);
	}

}