<?php
/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */

namespace Lsr\Helpers\Tracy;

use Exception;
use Lsr\Core\App;
use Lsr\Core\Caching\Cache;
use Lsr\Core\Templating\Latte;
use Tracy\IBarPanel;

class CacheTracyPanel implements IBarPanel
{
	private Latte $latte;
	private Cache $cache;

	/**
	 * @inheritDoc
	 */
	public function getTab() : string {
		try {
			$calls = $this->getCache()->getCalls();
		} catch (Exception $e) {
			$calls = 'Invalid cache class';
		}
		return $this->getLatte()
								->viewToString(
									'../vendor/lsr/helpers/templates/Caching/tab',
									[
										'calls' => $calls
									]
								);
	}

	/**
	 * @return Cache
	 * @throws Exception
	 */
	public function getCache() : Cache {
		if (!isset($this->cache)) {
			/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
			$cache = App::getService('cache');
			if (!$cache instanceof Cache) {
				throw new Exception('Invalid cache class in DI container - expects \Lsr\Core\Caching\Cache class, got '.$cache::class);
			}
			$this->cache = $cache;
		}
		return $this->cache;
	}

	/**
	 * @return Latte
	 */
	public function getLatte() : Latte {
		if (!isset($this->latte)) {
			/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
			$this->latte = App::getService('templating.latte');
		}
		return $this->latte;
	}

	/**
	 * @inheritDoc
	 */
	public function getPanel() : string {
		try {
			$hits = $this->getCache()::$hit;
			$miss = $this->getCache()::$miss;
			$loadedKeys = $this->getCache()::$loadedKeys;
			arsort($loadedKeys);
		} catch (Exception $e) {
			$hits = 0;
			$miss = 0;
			$loadedKeys = [];
		}
		return $this->getLatte()->viewToString('../vendor/lsr/helpers/templates/Caching/panel', [
			'hits'       => $hits,
			'miss'       => $miss,
			'loadedKeys' => $loadedKeys,
		]);
	}
}