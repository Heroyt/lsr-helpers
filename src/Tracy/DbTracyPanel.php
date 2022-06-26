<?php
/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */

namespace Lsr\Helpers\Tracy;

use Lsr\Core\App;
use Lsr\Core\Templating\Latte;
use Lsr\Helpers\Tracy\Events\DbEvent;
use Tracy\IBarPanel;

class DbTracyPanel implements IBarPanel
{

	/** @var DbEvent[] */
	static public array $events = [];
	private Latte       $latte;

	public static function logEvent(DbEvent $event) : void {
		self::$events[] = $event;
	}

	/**
	 * @inheritDoc
	 */
	public function getTab() : string {
		return $this->getLatte()->viewToString('debug/Db/tab', []);
	}

	/**
	 * @inheritDoc
	 */
	public function getPanel() : string {
		$panel = $this->getLatte()->viewToString('debug/Db/panel', []);
		updateTranslations();
		return $panel;
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
}