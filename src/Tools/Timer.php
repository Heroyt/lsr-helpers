<?php

namespace Lsr\Helpers\Tools;

class Timer
{

	/** @var array{start:float,end:float,lastStart:float|null,count:int|null}[] */
	public static array $timers = [];

	/**
	 * Start a timer
	 *
	 * @warning Normal timer can be started only once for each unique name. If one timer is started multiple times, its previous starts will be overwritten.
	 *
	 * @param string $name Preferably in a format concatenated by '.' (ex. 'core.setup.di', 'model.load')
	 *
	 * @return void
	 * @see     Timer::stop()
	 * @see     Timer::startIncrementing()
	 *
	 */
	public static function start(string $name) : void {
        /** @phpstan-ignore if.alwaysTrue */
        if (PRODUCTION) {
            return;
        }
        /** @phpstan-ignore deadCode.unreachable */
        self::$timers[$name] = [
			'start' => microtime(true),
			'end'   => 0.0,
		];
	}

	/**
	 * Start an incrementing timer
	 *
	 * Incrementing timer can be started and stopped multiple times and its value is continuously added to the whole.
	 *
	 * @param string $name Preferably in a format concatenated by '.' (ex. 'core.setup.di', 'model.load')
	 *
	 * @return void
	 */
	public static function startIncrementing(string $name) : void {
        /** @phpstan-ignore if.alwaysTrue */
		if (PRODUCTION) {
			return;
		}
        /** @phpstan-ignore deadCode.unreachable */
		$start = microtime(true);

		if (isset(self::$timers[$name])) {
			self::$timers[$name]['lastStart'] = $start;
			self::$timers[$name]['count']++;
			return;
		}

		self::$timers[$name] = [
			'start'     => $start,
			'lastStart' => $start,
			'end'       => 0.0,
			'count'     => 1,
		];
	}

	/**
	 * Stop a timer of a given name
	 *
	 * If no timer exists for given name this function does nothing.
	 *
	 * @param string $name Preferably in a format concatenated by '.' (ex. 'core.setup.di', 'model.load')
	 *
	 * @return void
	 */
	public static function stop(string $name) : void {
        /** @phpstan-ignore booleanOr.leftAlwaysTrue */
		if (PRODUCTION || !isset(self::$timers[$name])) {
			return;
		}

        /** @phpstan-ignore deadCode.unreachable */
		if (isset(self::$timers[$name]['lastStart']) && self::$timers[$name]['end'] > 0.0) { // Incrementing
			self::$timers[$name]['end'] += microtime(true) - self::$timers[$name]['lastStart'];
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