<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests;

use Wedo\Utilities\Aop\Annotations\Cached;
use Wedo\Utilities\Aop\Markers\ICached;

class TestCacheNoParams implements ICached
{

	private int $called = 0;

	/**
	 * @Cached
	 */
	public function test(): int
	{
		$this->called++;

		return $this->called;
	}

}
