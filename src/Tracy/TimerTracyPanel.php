<?php
/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */

namespace Lsr\Helpers\Tracy;

use Lsr\Core\App;
use Lsr\Core\Templating\Latte;
use Lsr\Helpers\Tools\Timer;
use Tracy\IBarPanel;

class TimerTracyPanel implements IBarPanel
{

	private Latte $latte;

	/**
	 * @inheritDoc
	 */
	public function getTab() : string {
		return $this->getLatte()->viewToString('../vendor/lsr/helpers/templates/Timer/tab', []);
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
		$times = [];
		foreach (Timer::$timers as $name => $info) {
			$exploded = explode('.', $name);
			$curr = &$times;
			foreach ($exploded as $part) {
				// Check if this namespace already has a value assigned to it
				if (isset($curr[$part]) && !is_array($curr[$part])) {
					// Split the value and child timers
					$curr[$part] = [
						'time' => $curr[$part],
						'sub'  => [],
					];
				}

				// Check for split values
				if (isset($curr['sub'])) {
					$curr = &$curr['sub'];
					// Check if this namespace already has a value assigned to it
					if (isset($curr[$part]) && !is_array($curr[$part])) {
						// Split the value and child timers
						$curr[$part] = [
							'time' => $curr[$part],
							'sub'  => [],
						];
					}
				}

				if (!isset($curr[$part])) {
					$curr[$part] = [];
				}
				$curr = &$curr[$part];
			}
			// Check if child timers are already defined
			if (is_array($curr) && !empty($curr)) {
				// Split the value and child timers
				$curr = [
					'time' => $this->formatInfo($info),
					'sub'  => $curr,
				];
			}
			else {
				$curr = $this->formatInfo($info);
			}
		}
		return $this->getLatte()->viewToString('../vendor/lsr/helpers/templates/Timer/panel', ['times' => $times]);
	}

	/**
	 * @param array{start:float,end:float,lastStart:float|null,count:int|null} $info
	 *
	 * @return string
	 */
	private function formatInfo(array $info) : string {
		$time = $info['end'] - $info['start'];
		$formatted = $this->formatTime($time);

		if (isset($info['count'])) {
			$formatted .= ' (called '.$info['count'].' times - '.$this->formatTime($time / $info['count']).' average)';
		}

		return $formatted;
	}

	private function formatTime(float $time) : string {
		if ($time < 1.0) {
			return ($time * 1000).'ms';
		}
		return $time.'s';
	}
}