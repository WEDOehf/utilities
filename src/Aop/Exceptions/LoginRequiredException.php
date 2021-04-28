<?php declare (strict_types = 1);

namespace Wedo\Utilities\Aop\Exceptions;

use Exception;
use Throwable;

class LoginRequiredException extends Exception
{

	public function __construct(string $message = '', int $code = 401, ?Throwable $previous = null)
	{
		if ($message === '') {
			$message = 'Operation is not available to guests!';
		}

		parent::__construct($message, $code, $previous);
	}

}
