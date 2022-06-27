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
				if (!isset($curr[$part])) {
					$curr[$part] = [];
				}
				$curr = &$curr[$part];
			}
			$curr = $info['end'] - $info['start'];
		}
		return $this->getLatte()->viewToString('../vendor/lsr/helpers/templates/Timer/panel', ['times' => $times]);
	}
}