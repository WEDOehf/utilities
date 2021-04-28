<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop;

use Contributte\Aop\Annotations\Before;
use Contributte\Aop\JoinPoint\BeforeMethod;
use Nette\NotSupportedException;
use Nette\Reflection\ClassType;
use Nette\Security\User;
use Nette\SmartObject;
use Wedo\Utilities\Aop\Exceptions\LoginRequiredException;
use Wedo\Utilities\Aop\Exceptions\TryAfterLoginException;

/**
 * @SuppressWarnings(PHPMD)
 */
class LoginRequiredAspect
{

	use SmartObject;

	private User $user;

	public function __construct(User $user)
	{
		$this->user = $user;
	}


	/**
	 * phpcs:ignore
	 * @Before("class(Wedo\Utilities\Aop\Markers\ILoginRequired) && methodAnnotatedWith(Wedo\Utilities\Aop\Annotations\LoginRequired)")
	 * @throws LoginRequiredException
	 * @throws NotSupportedException
	 */
	public function loginRequired(BeforeMethod $method): void
	{
		if ($method->getTargetReflection()->isConstructor()) {
			throw new NotSupportedException('LoginRequired annotation cannot be used on __construct()!');
		}

		if (!$this->user->isLoggedIn()) {
			throw new LoginRequiredException();
		}
	}


	/**
	 * phpcs:ignore
	 * @Before("class(Wedo\Utilities\Aop\Markers\ITryAfterLogin) && methodAnnotatedWith(Wedo\Utilities\Aop\Annotations\TryAfterLogin)")
	 * @throws NotSupportedException
	 */
	public function tryAfterLogin(BeforeMethod $method): void
	{
		$reflection = $method->getTargetReflection();
		$class = $reflection->getDeclaringClass();

		if ($reflection->isConstructor()) {
			throw new NotSupportedException('TryAfterLogin annotation cannot be used on __construct() or on non-api methods!');
		}

		if (!$this->user->isLoggedIn()) {
			$reflection = $method->getTargetReflection();
			$ex = new TryAfterLoginException();

			/** @var ClassType $presenterClass */
			$presenterClass = $class->getParentClass() ?? $class;
			$ex->presenter = $presenterClass->getName();
			$ex->action = $reflection->getName();
			$ex->parameters = $method->getArguments();

			throw $ex;
		}
	}

}
