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

	public static function logEvent(DbEvent $event) : void {
		self::$events[] = $event;
	}

	/**
	 * @inheritDoc
	 */
	public function getTab() : string {
		return <<<HTML
        <span title="Dotazy na databázy">
            <svg xmlns="http://www.w3.org/2000/svg" height="512pt" version="1.1" viewBox="-37 0 512 512" width="512pt">
                <g id="surface1">
                    <path d="M 438.855469 64 C 438.855469 28.652344 340.617188 0 219.429688 0 C 98.242188 0 0 28.652344 0 64 C 0 99.347656 98.242188 128 219.429688 128 C 340.617188 128 438.855469 99.347656 438.855469 64 Z M 438.855469 64 "
                          style=" stroke:none;fill-rule:nonzero;fill:rgb(74.117647%,85.882353%,100%);fill-opacity:1;"/>
                    <path d="M 219.429688 256 C 340.570312 256 438.855469 227.382812 438.855469 192 L 438.855469 64 C 438.855469 99.382812 340.570312 128 219.429688 128 C 98.285156 128 0 99.382812 0 64 L 0 192 C 0 227.382812 98.285156 256 219.429688 256 Z M 219.429688 256 "
                          style=" stroke:none;fill-rule:nonzero;fill:rgb(60.784314%,78.823529%,100%);fill-opacity:1;"/>
                    <path d="M 219.429688 384 C 340.570312 384 438.855469 355.382812 438.855469 320 L 438.855469 192 C 438.855469 227.382812 340.570312 256 219.429688 256 C 98.285156 256 0 227.382812 0 192 L 0 320 C 0 355.382812 98.285156 384 219.429688 384 Z M 219.429688 384 "
                          style=" stroke:none;fill-rule:nonzero;fill:rgb(34.117647%,64.313725%,100%);fill-opacity:1;"/>
                    <path d="M 438.855469 320 C 438.855469 355.382812 340.570312 384 219.429688 384 C 98.285156 384 0 355.382812 0 320 L 0 448 C 0 483.382812 98.285156 512 219.429688 512 C 340.570312 512 438.855469 483.382812 438.855469 448 Z M 438.855469 320 "
                          style=" stroke:none;fill-rule:nonzero;fill:rgb(14.117647%,53.333333%,100%);fill-opacity:1;"/>
                </g>
            </svg>
            <span class="tracy-label">DB</span>
        </span>
        HTML;
	}

	/**
	 * @inheritDoc
	 */
	public function getPanel() : string {
        $panel = <<<HTML
        <h1>DB</h1>
        <div class="tracy-inner">
            <div class="tracy-inner-container">
        HTML;
        $rows = lang('Rows', context: 'debugPanel');
        $takes = lang('Takes', context: 'debugPanel');
        foreach (self::$events as $event) {
            $class = $event->status === DbEvent::ERROR ? 'danger' : 'success';
            $time = number_format($event->time, 3, ',', '');
            $panel .= <<<HTML
                <div class="p-3 my-2 rounded border">
                    <h5 class="text-$class my-1 fs-5">{$event->status}</h5>
                    {$event->sql}
                    <p>$rows: {$event->count}</p>
                    <p>$takes: $time ms</p>
                    <div class="p-1 rounded bg-secondary text-light w-100">{$event->source}</div>
                </div>
            HTML;
        }
        $panel .= <<<HTML
            </div>
        </div>
        HTML;
		return $panel;
	}
}