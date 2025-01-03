<?php
/**
 * @author Tomáš Vojík <xvojik00@stud.fit.vutbr.cz>, <vojik@wboy.cz>
 */
namespace Lsr\Helpers\Tools;


class Strings extends \Nette\Utils\Strings
{

	/**
	 * Convert string to camelCase
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function toCamelCase(string $str) : string {
		return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $str))));
	}

	/**
	 * Convert string to PascalCase
	 *
	 * @param string $str
	 *
	 * @return string
	 */
	public static function toPascalCase(string $str) : string {
		return str_replace(' ', '', ucwords(str_replace('_', ' ', $str)));
	}

	/**
	 * Convert string to snake_case
	 *
	 * @param string $str
	 * @param string $separator
	 *
	 * @return string
	 */
	public static function toSnakeCase(string $str, string $separator = '_') : string {
		if (!ctype_lower($str)) {
			$str = (string) preg_replace('/\s+/u', '', ucwords($str));
            $str = preg_replace(
                '/(.)(?=[A-Z])/u',
                '$1'.$separator,
                $str
            );
            assert(is_string($str));
			$str = mb_strtolower($str);
		}

		return $str;
	}

}