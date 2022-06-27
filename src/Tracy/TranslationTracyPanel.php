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
		return $this->getLatte()->viewToString('../vendor/lsr/helpers/templates/Translation/tab', []);
	}

	/**
	 * @inheritDoc
	 */
	public function getPanel() : string {
		$panel = $this->getLatte()->viewToString('../vendor/lsr/helpers/templates/Translation/panel', []);
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