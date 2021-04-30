<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop\Attributes;

use Attribute;

#[Attribute]
class FileCached
{

	public ?int $seconds;

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct(?int $seconds)
	{
		$this->seconds = $seconds;
	}

}
