<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop\Attributes;

use Attribute;

#[Attribute]
class Cached
{

	public ?int $seconds;

	public ?string $key;

	/**
	 * @codeCoverageIgnore
	 */
	public function __construct(?int $seconds = null, ?string $key = null)
	{
		$this->seconds = $seconds;
		$this->key = $key;
	}

}
