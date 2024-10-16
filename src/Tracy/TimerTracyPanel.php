<?php
/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */

namespace Lsr\Helpers\Tracy;

use Lsr\Helpers\Tools\Timer;
use Tracy\Dumper;
use Tracy\IBarPanel;

class TimerTracyPanel implements IBarPanel
{

    /**
     * @inheritDoc
     */
    public function getTab() : string {
        return <<<HTML
        <span title="Timer">
            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="512" height="512" x="0" y="0" viewBox="0 0 512 512"
                 style="enable-background:new 0 0 512 512" xml:space="preserve" class="">
                <g>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M149.944,101.607l-29.896-29.958c-6.935-6.949-16.16-10.775-25.977-10.775  c-9.817,0-19.042,3.827-25.976,10.775L46.424,93.367c-14.265,14.293-14.265,37.551,0,51.845l29.959,30.021  c2.93,2.936,6.773,4.404,10.618,4.404c3.833,0,7.668-1.461,10.596-4.382c5.864-5.852,46.47-46.591,52.326-52.435  C155.786,116.969,155.796,107.472,149.944,101.607z"
                          fill="#105c6e" data-original="#105c6e" class=""></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M256,3c-8.284,0-15,6.716-15,15v48c0,8.284,6.716,15,15,15c8.284,0,15-6.716,15-15V18  C271,9.716,264.284,3,256,3z"
                          fill="#26879c" data-original="#26879c"></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M293,0h-74c-8.284,0-15,6.716-15,15s6.716,15,15,15h74c8.284,0,15-6.716,15-15S301.284,0,293,0z"
                          fill="#de513c"
                          data-original="#de513c"></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M256,0h-37c-8.284,0-15,6.716-15,15s6.716,15,15,15h37V0z"
                          fill="#fc6249" data-original="#fc6249" class=""></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M418.645,118.615C375.203,75.083,317.441,51.108,256,51.108S136.797,75.083,93.355,118.615  c-43.434,43.524-67.354,101.391-67.354,162.939s23.92,119.415,67.354,162.939C136.797,488.025,194.559,512,256,512  s119.203-23.975,162.645-67.507c43.434-43.524,67.354-101.391,67.354-162.939S462.079,162.139,418.645,118.615z"
                          fill="#de513c" data-original="#de513c"></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M256,51.108c-61.441,0-119.203,23.975-162.645,67.507c-43.434,43.524-67.354,101.391-67.354,162.939  s23.92,119.415,67.354,162.939C136.797,488.025,194.559,512,256,512V51.108z"
                          fill="#fc6249" data-original="#fc6249" class=""></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M256,108.538c-95.218,0-172.684,77.614-172.684,173.015S160.782,454.569,256,454.569  s172.684-77.615,172.684-173.016S351.218,108.538,256,108.538z"
                          fill="#96d1d9" data-original="#96d1d9"></path>
                    <path xmlns="http://www.w3.org/2000/svg" style=""
                          d="M256,108.538c-95.218,0-172.684,77.614-172.684,173.015S160.782,454.569,256,454.569V108.538z"
                          fill="#f4f2e6"
                          data-original="#f4f2e6" class=""></path>
                    <g xmlns="http://www.w3.org/2000/svg">
                        <path style=""
                              d="M256,146.007c8.284,0,15-6.716,15-15v-21.808c-4.945-0.428-9.946-0.66-15-0.66   c-5.054,0-10.055,0.232-15,0.66v21.808C241,139.291,247.716,146.007,256,146.007z"
                              fill="#105c6e" data-original="#105c6e" class=""></path>
                        <path style=""
                              d="M256,417.101c-8.284,0-15,6.716-15,15v21.808c4.945,0.428,9.946,0.66,15,0.66   c5.054,0,10.055-0.232,15-0.66v-21.808C271,423.817,264.284,417.101,256,417.101z"
                              fill="#105c6e" data-original="#105c6e" class=""></path>
                        <path style=""
                              d="M428.028,266.554h-21.481c-8.284,0-15,6.716-15,15s6.716,15,15,15h21.481   c0.426-4.945,0.656-9.946,0.656-15S428.454,271.499,428.028,266.554z"
                              fill="#105c6e" data-original="#105c6e" class=""></path>
                        <path style=""
                              d="M120.453,281.554c0-8.284-6.716-15-15-15H83.972c-0.426,4.945-0.656,9.946-0.656,15   s0.23,10.055,0.656,15h21.481C113.737,296.554,120.453,289.838,120.453,281.554z"
                              fill="#105c6e" data-original="#105c6e" class=""></path>
                        <path style=""
                              d="M293,272.897h-21.162V212.23c0-8.284-6.716-15-15-15c-8.284,0-15,6.716-15,15v75.667   c0,8.284,6.716,15,15,15H293c8.284,0,15-6.716,15-15S301.284,272.897,293,272.897z"
                              fill="#105c6e" data-original="#105c6e" class=""></path>
                    </g>
                </g>
            </svg>
            <span class="tracy-label">Timer</span>
        </span>
        HTML;

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
        $timesDump = Dumper::toHtml($times);
        return <<<HTML
        <h1>Timers</h1>
        <div class="tracy-inner">
            <div class="tracy-inner-container">
                {$timesDump}
            </div>
        </div>
        HTML;
    }

    /**
     * @param  array{start:float,end:float,lastStart:float|null,count:int|null}  $info
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