<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop\Exceptions;

class TryAfterLoginException extends LoginRequiredException
{

	public string $presenter;

	public string $action;

	/** @var mixed[] */
	public array $parameters;

}
