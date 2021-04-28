<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wedo\Utilities\CacheHelper;

class CacheHelperTest extends TestCase
{

	public function testGenerateKey_ShouldCreateCorrectKey(): void
	{
		$this->assertEquals('MethodCache:Wedo\Utilities\Tests\CacheHelperTest:test', CacheHelper::generateKey($this, 'test'));
		$this->assertEquals('MethodCache:Wedo\Utilities\Tests\CacheHelperTest:test:a:b:c', CacheHelper::generateKey($this, 'test', ['a', 'b', 'c']));
		$this->assertEquals('MethodCache:Wedo\Utilities\Tests\CacheHelperTest:test:99914b932bd37a50b983c5e7c90ae93b', CacheHelper::generateKey($this, 'test', [new stdClass()]));
	}

}
