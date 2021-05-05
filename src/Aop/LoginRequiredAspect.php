<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop;

use Contributte\Aop\Attributes\Before;
use Contributte\Aop\JoinPoint\BeforeMethod;
use Nette\NotSupportedException;
use Nette\Security\User;
use Nette\SmartObject;
use ReflectionClass;
use Wedo\Utilities\Aop\Exceptions\LoginRequiredException;
use Wedo\Utilities\Aop\Exceptions\TryAfterLoginException;

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
	 * @throws LoginRequiredException
	 * @throws NotSupportedException
	 */
	#[Before('class(Wedo\Utilities\Aop\Markers\ILoginRequired) && methodAttributedWith(Wedo\Utilities\Aop\Attributes\LoginRequired)')]

	public function loginRequired(BeforeMethod $method): void
	{
		if ($method->getTargetReflection()->isConstructor()) {
			throw new NotSupportedException('LoginRequired atributte cannot be used on __construct()!');
		}

		if (!$this->user->isLoggedIn()) {
			throw new LoginRequiredException();
		}
	}


	/**
	 * phpcs:ignore
	 * @throws NotSupportedException
	 */
	#[Before('class(Wedo\Utilities\Aop\Markers\ITryAfterLogin) && methodAttributedWith(Wedo\Utilities\Aop\Attributes\TryAfterLogin)')]

	public function tryAfterLogin(BeforeMethod $method): void
	{
		$reflection = $method->getTargetReflection();
		$class = $reflection->getDeclaringClass();

		if ($reflection->isConstructor()) {
			throw new NotSupportedException('TryAfterLogin attribute cannot be used on __construct() or on non-api methods!');
		}

		if (!$this->user->isLoggedIn()) {
			$reflection = $method->getTargetReflection();
			$ex = new TryAfterLoginException();

			/** @var ReflectionClass<object> $presenterClass */
			$presenterClass = $class->getParentClass() === false ? $class : $class->getParentClass();
			$ex->presenter = $presenterClass->getName();
			$ex->action = $reflection->getName();
			$ex->parameters = $method->getArguments();

			throw $ex;
		}
	}

}
