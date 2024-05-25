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

use Lsr\Core\App;
use Lsr\Exceptions\FileException;

if (!function_exists('formToken')) {
    /**
     * Generate a form token to protect against CSRF
     *
     * @param  string  $prefix
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
     * @param  string  $hash
     *
     * @param  string  $check
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
     * @param  string  $name
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
     * @param  string  $content
     * @param  string  $type
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
     * @param  float  $x
     * @param  float  $minIn
     * @param  float  $maxIn
     * @param  float  $minOut
     * @param  float  $maxOut
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
     * @param  string|null  $msg  Massage to translate
     * @param  string|null  $plural
     * @param  int  $num
     * @param  string|null  $context
     * @param  string|null  $domain
     * @param  array  $format
     * @return string Translated message
     *
     * @version 1.0
     * @author  Tomáš Vojík <vojik@wboy.cz>
     */
    function lang(
      ?string $msg = null,
      ?string $plural = null,
      int     $num = 1,
      ?string $context = null,
      ?string $domain = null,
      array   $format = []
    ) : string {
        return App::getInstance()->translations->translate(
                   $msg,
          plural : $plural,
          num    : $num,
          domain : $domain,
          context: $context,
          format : $format
        );
    }
}

if (!function_exists('updateTranslations')) {
    /**
     * Regenerate the translation .po files
     */
    function updateTranslations() : void {
        App::getInstance()->translations->updateTranslations();
    }
}

if (!function_exists('ratio')) {
    /**
     * Gets simplified ratio of two numbers
     *
     * @param  int  $var1  First number
     * @param  int  $var2  Second number
     * @param  int|null  $return  Index of what number to return to - null to return whole array
     *
     * @return int|array{0:float, 1:float} One simplified number or the whole ratio as an array
     */
    function ratio(int $var1, int $var2, int $return = null) : array | int {
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
     * @param  string  $name
     * @param  string|int  $width
     * @param  string|int  $height
     *
     * @return string
     * @throws FileException
     */
    function svgIcon(string $name, string | int $width = '100%', string | int $height = '') : string {
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
     * @param  string  $string
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
     * @param  T[]  $array
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
     * @param  T[]  $array
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