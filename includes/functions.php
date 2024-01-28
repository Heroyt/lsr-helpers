<?php
/**
 * @file      functions.php
 * @brief     Main functions
 * @details   File containing all main functions for the app.
 * @author    Tomáš Vojík <vojik@wboy.cz>
 * @date      2021-09-22
 * @version   1.0
 * @since     1.0
 */

/** @noinspection PhpUnused */

use Gettext\Generator\MoGenerator;
use Gettext\Generator\PoGenerator;
use Gettext\Translation;
use Gettext\Translations;
use Lsr\Exceptions\FileException;
use Lsr\Helpers\Tools\Timer;
use Lsr\Helpers\Tracy\Events\TranslationEvent;
use Lsr\Helpers\Tracy\TranslationTracyPanel;

if (!function_exists('formToken')) {
	/**
	 * Generate a form token to protect against CSRF
	 *
	 * @param string $prefix
	 *
	 * @return string
	 */
	function formToken(string $prefix = '') : string {
		if (empty($_SESSION[$prefix.'_csrf_hash'])) {
			$_SESSION[$prefix.'_csrf_hash'] = bin2hex(random_bytes(32));
		}
		return $_SESSION[$prefix.'_csrf_hash'];
	}
}

if (!function_exists('isTokenValid')) {
	/**
	 * Validate a CSRF token
	 *
	 * @param string $hash
	 *
	 * @param string $check
	 *
	 * @return bool
	 */
	function isTokenValid(string $hash, string $check = '') : bool {
		if (empty($check)) {
			$check = (string) ($_SESSION['_csrf_hash'] ?? '');
		}
		return hash_equals($check, $hash);
	}
}

if (!function_exists('formValid')) {
	/**
	 * Validate submitted form against csrf
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	function formValid(string $name) : bool {
		$hash = hash_hmac('sha256', $name, $_SESSION[$name.'_csrf_hash'] ?? '');
		return isTokenValid($_REQUEST['_csrf_token'], $hash);
	}
}

if (!function_exists('alert')) {
	/**
	 * Print a bootstrap alert
	 *
	 * @param string $content
	 * @param string $type
	 *
	 * @return string
	 */
	function alert(string $content, string $type = 'danger') : string {
		return '<div class="alert alert-'.$type.'">'.$content.'</div>';
	}
}

if (!function_exists('not_empty')) {
	function not_empty(mixed $var) : bool {
		return !empty($var);
	}
}

if (!function_exists('map')) {
	/**
	 * @param float $x
	 * @param float $minIn
	 * @param float $maxIn
	 * @param float $minOut
	 * @param float $maxOut
	 *
	 * @return float
	 */
	function map(float $x, float $minIn, float $maxIn, float $minOut, float $maxOut) : float {
		return ($x - $minIn) * ($maxOut - $minOut) / ($maxIn - $minIn) + $minOut;
	}
}

if (!function_exists('lang')) {
	/**
	 * Wrapper for gettext function
	 *
	 * @param string|null $msg Massage to translate
	 * @param string|null $plural
	 * @param int         $num
	 * @param string|null $context
	 *
	 * @return string Translated message
	 *
	 * @version 1.0
	 * @author  Tomáš Vojík <vojik@wboy.cz>
	 */
	function lang(?string $msg = null, ?string $plural = null, int $num = 1, ?string $context = null) : string {

		if (empty($msg)) {
			return '';
		}
		Timer::startIncrementing('translation');

		// Add context
		$msgTmp = $msg;
		if (!empty($context)) {
			$msg = $context."\004".$msg;
		}

		// If in development - add translation to po file if not exist
		/** @phpstan-ignore-next-line */
		if (!PRODUCTION && CHECK_TRANSLATIONS) {
			$logged = false;

			$file = '';

			if (TRANSLATIONS_COMMENTS) {
				// Use an exception to get the trace to this function call
				$trace = (new Exception)->getTrace();
				if (is_array($trace) && isset($trace[0])) {
					/** @phpstan-ignore-next-line */
					$file = str_replace(ROOT, '/', $trace[0]['file']).':'.$trace[0]['line'];
					/** @phpstan-ignore-next-line */
					if (str_contains($trace[0]['file'], 'latte')) {
						// Load parsed latte file by lines
						/** @var string[] $lines */
						$lines = file($trace[0]['file']);
						// Source comment is on line 5
						$line = $lines[4] ?? '';
						if (preg_match('/\/\*+ source: ([^*]+) \*\//', $line, $matches)) {
							$file = str_replace(ROOT, '/', $matches[1]).':';
							// Find line number
							// Line number should be located in a comment somewhere on or bellow the called line
							$lineCount = count($lines);
							/** @phpstan-ignore-next-line */
							for ($i = $trace[0]['line'] - 1; $i < $lineCount; $i++) {
								if (preg_match('/\/\*+ line (\d+) \*\//', $lines[$i], $matches)) {
									// Found line number
									$file .= $matches[1];
									break;
								}
							}
						}
					}
				}
			}
			foreach ($GLOBALS['translations'] as $translations) {
				/** @var Translations $translations */
				if (!($translation = $translations->find($context, $msgTmp))) {                    // Check if translation exists
					// Create new translation
					if (!$logged) {
						$event = new TranslationEvent();
						$event->message = $msgTmp;
						$event->plural = $plural;
						$event->context = $context;
						$trace = debug_backtrace(limit: 1);
						/** @phpstan-ignore-next-line */
						$event->source = $trace[0]['file'].':'.$trace[0]['line'].' '.$trace[0]['function'].'()';
						TranslationTracyPanel::logEvent($event);
						$logged = true;
					}
					$translation = Translation::create($context, $msgTmp);
					if ($plural !== null) {
						$translation->setPlural($plural);
					}

					$translations->add($translation);
					$GLOBALS['translationChange'] = true;
				}
				$comments = $translation->getComments();
				if (!empty($file) && !in_array($file, $comments->toArray(), true)) {
					$comments->add($file);
					$GLOBALS['translationChange'] = true;
				}
			}
		}

		// Translate
		if ($num === 1) {
			$translated = gettext($msg);
		}
		else {
			$translated = ngettext($msg, $plural ?? $msg, $num);
		}

		// If the translation with the context does not exist, try to translate it without it
		$split = explode("\004", $translated);
		if (count($split) === 2) {
			if ($num === 1) {
				$translated = gettext($split[1]);
			}
			else {
				$translated = ngettext($split[1], $plural ?? $msg, $num);
			}
		}
		TranslationTracyPanel::incrementTranslations();
		Timer::stop('translation');
		return $translated;
	}
}

if (!function_exists('updateTranslations')) {
	/**
	 * Regenerate the translation .po files
	 */
	function updateTranslations() : void {
		/** @var Translations[] $translations */
		global $translationChange, $translations;
		/** @phpstan-ignore-next-line */
		if (PRODUCTION || !$translationChange) {
			return;
		}
		/** @phpstan-ignore-next-line */
		Timer::startIncrementing('translation.update');
		$poGenerator = new PoGenerator();
		$moGenerator = new MoGenerator();
		$template = null;
		foreach ($translations as $lang => $translation) {
			if (!isset($template)) {
				$template = clone $translation;
			}
			$poGenerator->generateFile($translation, LANGUAGE_DIR.$lang.'/LC_MESSAGES/'.LANGUAGE_FILE_NAME.'.po');
			$moGenerator->generateFile($translation, LANGUAGE_DIR.$lang.'/LC_MESSAGES/'.LANGUAGE_FILE_NAME.'.mo');
		}
		if (isset($template)) {
			foreach ($template->getTranslations() as $string) {
				$string->translate('');
				$pluralCount = count($string->getPluralTranslations());
				if ($pluralCount > 0) {
					$plural = [];
					for ($i = 0; $i < $pluralCount; $i++) {
						$plural[] = '';
					}
					$string->translatePlural(...$plural);
				}
				$poGenerator->generateFile($template, LANGUAGE_DIR.LANGUAGE_FILE_NAME.'.pot');
			}
		}
		Timer::stop('translation.update');
	}
}

if (!function_exists('ratio')) {
	/**
	 * Gets simplified ratio of two numbers
	 *
	 * @param int      $var1   First number
	 * @param int      $var2   Second number
	 * @param int|null $return Index of what number to return to - null to return whole array
	 *
	 * @return int|array{0:float, 1:float} One simplified number or the whole ratio as an array
	 */
	function ratio(int $var1, int $var2, int $return = null) : array|int {
		for ($x = $var1; $x > 1; $x--) {
			if (($var1 % $x) === 0 && ($var2 % $x) === 0) {
				$var1 /= $x;
				$var2 /= $x;
			}
		}
		$arr = [$var1, $var2];
		if (!isset($return)) {
			return $arr;
		}
		return $arr[$return] ?? 0;
	}
}

if (!function_exists('svgIcon')) {
	/**
	 * @param string     $name
	 * @param string|int $width
	 * @param string|int $height
	 *
	 * @return string
	 * @throws FileException
	 */
	function svgIcon(string $name, string|int $width = '100%', string|int $height = '') : string {
		$file = ASSETS_DIR.'icons/'.$name.'.svg';
		if (!file_exists($file)) {
			throw new InvalidArgumentException('Icon "'.$name.'" does not exist in "'.ASSETS_DIR.'icons/".');
		}
		$contents = file_get_contents($file);
		if ($contents === false) {
			throw new FileException('Failed to read file '.$file);
		}
		$xml = simplexml_load_string($contents);
		if ($xml === false) {
			throw new FileException('File ('.$file.') does not contain valid SVG');
		}
		unset($xml['width'], $xml['height']);
		/** @phpstan-ignore-next-line */
		$xml['id'] = '';
		/** @phpstan-ignore-next-line */
		$xml['class'] = 'icon-'.$name;
		if (is_int($width)) {
			$width .= 'px';
		}
		if (is_int($height)) {
			$height .= 'px';
		}
		/** @phpstan-ignore-next-line */
		$xml['style'] = '';
		if (!empty($width)) {
			/** @phpstan-ignore-next-line */
			$xml['style'] .= 'width:'.$width.';';
		}
		if (!empty($height)) {
			/** @phpstan-ignore-next-line */
			$xml['style'] .= 'height:'.$height.';';
		}
		$out = $xml->asXML();
		if ($out === false) {
			return '';
		}
		return $out;
	}
}

if (!function_exists('trailingSlashIt')) {
	/**
	 * Add a trailing slash to a string (file/directory path)
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	function trailingSlashIt(string $string) : string {
		if (substr($string, -1) !== DIRECTORY_SEPARATOR) {
			$string .= DIRECTORY_SEPARATOR;
		}
		return $string;
	}
}

if (!function_exists('first')) {
	/**
	 * Get the first element from any array
	 *
	 * @template T
	 *
	 * @param T[] $array
	 *
	 * @return T|null First element or null if the array is empty
	 */
	function first(array $array) : mixed {
		/** @noinspection LoopWhichDoesNotLoopInspection */
		foreach ($array as $val) {
			return $val;
		}
		return null;
	}
}

if (!function_exists('last')) {
	/**
	 * Get the last element from any array
	 *
	 * @template T
	 *
	 * @param T[] $array
	 *
	 * @return T|null
	 */
	function last(array $array) : mixed {
		$last = end($array);
		if ($last === false) {
			return null;
		}
		return $last;
	}
}