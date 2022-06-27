<?php

namespace Lsr\Helpers\Tools;

class Timer
{

	/** @var array{start:float,end:float}[] */
	public static array $timers = [];

	public static function start(string $name) : void {
		if (PRODUCTION) {
			return;
		}
		self::$timers[$name] = [
			'start' => microtime(true),
			'end'   => 0.0,
		];
	}

	public static function stop(string $name) : void {
		if (PRODUCTION) {
			return;
		}
		if (!isset(self::$timers[$name])) {
			return;
		}
		self::$timers[$name]['end'] = microtime(true);
	}

	public static function get(string $name) : float {
		if (!isset(self::$timers[$name])) {
			return 0.0;
		}
		return self::$timers[$name]['end'] - self::$timers[$name]['start'];
	}

}