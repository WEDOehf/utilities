<?php declare (strict_types = 1);

namespace Wedo\Utilities\Tests\Aop;

use Contributte\Aop\JoinPoint\AroundMethod;
use Nette\Caching\Cache;
use Nette\Caching\Storages\MemoryStorage;
use PHPUnit\Framework\TestCase;
use Wedo\Utilities\Aop\CachedAspect;
use Wedo\Utilities\Tests\TestCache;
use Wedo\Utilities\Tests\TestCacheNoParams;

class CachedAspectTest extends TestCase
{

	private CachedAspect $aspect;

	private AroundMethod $aroundMethod;

	private Cache $cache;

	protected function setUp(): void
	{
		$this->cache = new Cache(new MemoryStorage());
		$testObj = new TestCache();

		$cacheDir = __DIR__ . '/../../../../../temp/cache';
		if (!is_dir($cacheDir)) {
			mkdir($cacheDir, 0777, true);
		}

		$this->aroundMethod = new AroundMethod($testObj, 'test');
		$this->aroundMethod->addChainLink($testObj, 'test');
		$this->aspect = new CachedAspect($this->cache, $cacheDir);
	}

	public function testMemoryCache(): void
	{
		$first = $this->aspect->memoryCache($this->aroundMethod);
		$this->assertEquals(1, $first);
		$second = $this->aspect->memoryCache($this->aroundMethod);
		$this->assertEquals(1, $second);
	}


	public function testCache(): void
	{
		$first = $this->aspect->cache($this->aroundMethod);
		$this->assertEquals(1, $first);
		$second = $this->aspect->cache($this->aroundMethod);
		$this->assertEquals(1, $second);
	}

	public function testCache_WithNoParams(): void
	{
		$testObj = new TestCacheNoParams();
		$this->aroundMethod = new AroundMethod($testObj, 'test');
		$this->aroundMethod->addChainLink($testObj, 'test');

		$first = $this->aspect->cache($this->aroundMethod);
		$this->assertEquals(1, $first);
		$second = $this->aspect->cache($this->aroundMethod);
		$this->assertEquals(1, $second);
	}

	public function testFileCache(): void
	{
		$first = $this->aspect->fileCache($this->aroundMethod);
		$this->assertEquals(1, $first);
		$second = $this->aspect->fileCache($this->aroundMethod);
		$this->assertEquals(1, $second);
	}

	public function testFileCache_WithNoParams(): void
	{
		$testObj = new TestCacheNoParams();
		$this->aroundMethod = new AroundMethod($testObj, 'test');
		$this->aroundMethod->addChainLink($testObj, 'test');

		$first = $this->aspect->fileCache($this->aroundMethod);
		$this->assertEquals(1, $first);
		$second = $this->aspect->fileCache($this->aroundMethod);
		$this->assertEquals(1, $second);
	}

}
