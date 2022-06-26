<?php
/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */
namespace Lsr\Helpers\Tracy;

use Lsr\Core\App;
use Lsr\Core\Templating\Latte;
use Lsr\Helpers\Tracy\Events\TranslationEvent;
use Tracy\IBarPanel;

class TranslationTracyPanel implements IBarPanel
{

	/** @var TranslationEvent[] */
	static public array $events       = [];
	static public int   $translations = 0;
	private Latte       $latte;

	public function __construct() {
		/** @noinspection PhpFieldAssignmentTypeMismatchInspection */
		$this->latte = App::getService('templating.latte');
	}

	public static function logEvent(TranslationEvent $event) : void {
		self::$events[] = $event;
	}

	public static function incrementTranslations() : void {
		self::$translations++;
	}

	/**
	 * @inheritDoc
	 */
	public function getTab() : string {
		return $this->latte->view('debug/Translation/tab', [], true);
	}

	/**
	 * @inheritDoc
	 */
	public function getPanel() : string {
		$panel = $this->latte->view('debug/Translation/panel', [], true);
		updateTranslations();
		return $panel;
	}
}