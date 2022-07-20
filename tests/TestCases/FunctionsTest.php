<?php

namespace TestCases;

use PHPUnit\Framework\TestCase;

class FunctionsTest extends TestCase
{

	/** @phpstan-ignore-next-line */
	public function getArrays() : array {
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
	 * @dataProvider getArrays
	 *
	 * @return void
	 * @phpstan-ignore-next-line
	 */
	public function testFirst(array $array, mixed $first, mixed $last) : void {
		self::assertSame(first($array), $first);
	}

	/**
	 * @param array $array
	 * @param mixed $first
	 * @param mixed $last
	 *
	 * @dataProvider getArrays
	 *
	 * @return void
	 * @phpstan-ignore-next-line
	 */
	public function testLast(array $array, mixed $first, mixed $last) : void {
		self::assertSame(last($array), $last);
	}

}
