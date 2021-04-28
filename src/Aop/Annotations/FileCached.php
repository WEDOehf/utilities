<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop\Annotations;

/**
 * @Annotation
 */
class FileCached
{

	public ?int $seconds;

	/**
	 * @param mixed[] $params
	 * @codeCoverageIgnore
	 */
	public function __construct(array $params)
	{
		$this->seconds = $params['seconds'] ?? null;
	}

}
