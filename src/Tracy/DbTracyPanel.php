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

	public function __construct() {
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->latte = App::getService('templating.latte');
	}

	public static function logEvent(DbEvent $event) : void {
		self::$events[] = $event;
	}

	/**
	 * @inheritDoc
	 */
	public function getTab() : string {
		return $this->latte->view('debug/Db/tab', [], true);
	}

	/**
	 * @inheritDoc
	 */
	public function getPanel() : string {
		$panel = $this->latte->view('debug/Db/panel', [], true);
		updateTranslations();
		return $panel;
	}
}