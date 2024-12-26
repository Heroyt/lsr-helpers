<?php

namespace TestCases;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

	/** @phpstan-ignore-next-line */
    public static function getArrays() : array {
        return [
            [
                [],
                null,
                null,
            ],
            [
                ['a'],
                'a',
                'a',
            ],
            [
                ['a', 'b'],
                'a',
                'b',
            ],
            [
                ['a', 'b', 'c', 'd', 'e', 'f'],
                'a',
                'f',
            ],
            [
                ['a' => 'a', 'b' => 'b', 'c' => 'c', 'd' => 'd', 'e' => 'e', 'f' => 'f'],
                'a',
                'f',
            ],
        ];
    }

	/**
	 * @param array $array
	 * @param mixed $first
	 * @param mixed $last
	 *
     *
	 * @return void
	 * @phpstan-ignore-next-line
	 */
	#[DataProvider('getArrays')]
    public function testFirst(array $array, mixed $first, mixed $last) : void {
		self::assertSame(first($array), $first);
	}

	/**
	 * @param array $array
	 * @param mixed $first
	 * @param mixed $last
	 *
     *
	 * @return void
	 * @phpstan-ignore-next-line
	 */
	#[DataProvider('getArrays')]
    public function testLast(array $array, mixed $first, mixed $last) : void {
		self::assertSame(last($array), $last);
	}

}
