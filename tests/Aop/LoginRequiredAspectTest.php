<?php declare (strict_types = 1);

namespace Wedo\Utilities\Tests\Aop;

use Contributte\Aop\JoinPoint\BeforeMethod;
use Nette\NotSupportedException;
use Nette\Security\User;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Wedo\Utilities\Aop\Exceptions\LoginRequiredException;
use Wedo\Utilities\Aop\Exceptions\TryAfterLoginException;
use Wedo\Utilities\Aop\LoginRequiredAspect;

class LoginRequiredAspectTest extends TestCase
{

	private LoginRequiredAspect $aspect;

	private TestLoginRequired $testObj;

	private User|MockObject $user;

	public function testLoginRequired_OnRegularMethodWithNoUserLoggedIn_ShouldThrowLoginRequiredException(): void
	{
		$this->expectException(LoginRequiredException::class);
		$this->aspect->loginRequired(new BeforeMethod($this->testObj, 'test'));
	}

	public function testLoginRequired_OnConstructor_ShouldThrowNotSupportedException(): void
	{
		$this->expectException(NotSupportedException::class);
		$this->aspect->loginRequired(new BeforeMethod($this->testObj, '__construct'));
	}

	public function testLoginRequired_WithLoggedInUser_ShouldNotThrowException(): void
	{
		$this->user->expects($this->once())->method('isLoggedIn')->willReturn(true);
		$this->aspect->loginRequired(new BeforeMethod($this->testObj, 'test'));
	}

	public function testTryAfterLoginRequired_OnConstructor_ShouldThrowNotSupportedException(): void
	{
		$this->expectException(NotSupportedException::class);
		$this->aspect->tryAfterLogin(new BeforeMethod($this->testObj, '__construct'));
	}

	public function testTryAfterLogin_WithoutUser_ShouldThrowTryAfterLoginException(): void
	{
		$this->expectException(TryAfterLoginException::class);
		$this->aspect->tryAfterLogin(new BeforeMethod($this->testObj, 'test'));
	}

	public function testTryAfterLogin_WithLoggedInUser_ShouldNotThrowException(): void
	{
		$this->user->expects($this->once())->method('isLoggedIn')->willReturn(true);
		$this->aspect->tryAfterLogin(new BeforeMethod($this->testObj, 'test'));
	}

	protected function setUp(): void
	{
		$this->user = $this->createMock(User::class);

		$this->testObj = new TestLoginRequired();
		$this->aspect = new LoginRequiredAspect($this->user);
	}

}
