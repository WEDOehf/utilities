<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests\Aop;

class TestLoginRequired
{

	private int $called = 0;

	public function __construct()
	{
		$this->called = 2;
	}

	public function test(): int
	{
		$this->called++;

		return $this->called;
	}

}
