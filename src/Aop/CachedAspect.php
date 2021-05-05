<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop;

use Contributte\Aop\Attributes\Around;
use Contributte\Aop\JoinPoint\AroundMethod;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\SmartObject;
use Wedo\Utilities\Aop\Attributes\Cached;
use Wedo\Utilities\Aop\Attributes\FileCached;
use Wedo\Utilities\Aop\Attributes\MemoryCached;
use Wedo\Utilities\CacheHelper;

class CachedAspect
{

	use SmartObject;

	private Cache $cache;

	/** @var array<string, mixed> */
	private array $mCache = [];

	private Cache $fileStorage;

	public function __construct(Cache $cache, string $tmpDir)
	{
		$this->cache = $cache;
		$this->fileStorage = new Cache(new FileStorage($tmpDir));
	}


	#[Around('class(Wedo\Utilities\Aop\Markers\ICached) && methodAttributedWith(Wedo\Utilities\Aop\Attributes\Cached)')]

	public function cache(AroundMethod $method): mixed
	{
		$cache_key = $this->getKey($method, Cached::class);

		$result = $this->cache->load($cache_key);

		if ($result === null) {
			$result = $method->proceed();

			/** @var Cached $attribute */
			$attribute = $method->getTargetReflection()->getAttributes(Cached::class)[0]->newInstance();
			$seconds = $attribute->seconds ?? 60 * 15;
			$this->cache->save($cache_key, $result, [Cache::EXPIRATION => time() + $seconds]);
		}

		return $result;
	}

	#[Around('class(Wedo\Utilities\Aop\Markers\ICached) && methodAttributedWith(Wedo\Utilities\Aop\Attributes\MemoryCached)')]

	public function memoryCache(AroundMethod $method): mixed
	{
		$cache_key = $this->getKey($method, MemoryCached::class);

		if (isset($this->mCache[$cache_key])) {
			return $this->mCache[$cache_key];
		}

		$this->mCache[$cache_key] = $method->proceed();

		return $this->mCache[$cache_key];
	}

	#[Around('class(App\Utilities\Aop\Markers\ICached) && methodAttributedWith(App\Utilities\Aop\Attributes\FileCached)')]

	public function fileCache(AroundMethod $method): mixed
	{
		$cache_key = $this->getKey($method, FileCached::class);
		$result = $this->fileStorage->load($cache_key);

		if ($result === null) {
			$result = $method->proceed();

			$attributes = $method->getTargetReflection()->getAttributes('FileCached');

			/** @var FileCached|null $attribute */
			$attribute = isset($attributes[0]) ? $attributes[0]->newInstance() : null;

			$seconds = $attribute->seconds ?? 60 * 15;

			$this->fileStorage->save($cache_key, $result, [Cache::EXPIRATION => time() + $seconds]);
		}

		return $result;
	}

	/**
	 * @param class-string $className
	 */
	protected function getKey(AroundMethod $method, string $className): string
	{
		$attributes = $method->getTargetReflection()->getAttributes($className);

		if ($attributes === []) {
			return $this->generateDefaultKey($method);
		}

		/** @var Cached|MemoryCached|FileCached $attribute */
		$attribute = $attributes[0]->newInstance();

		if (isset($attribute->key) && $attribute->key !== null) {
			$key = $attribute->key;

			return $key . CacheHelper::generateKeyFromParams($method->getArguments());
		}

		return $this->generateDefaultKey($method);
	}

	private function generateDefaultKey(AroundMethod $method): string
	{
		return CacheHelper::generateKey(
			$method->getTargetObject(),
			$method->getTargetReflection()->getName(),
			$method->getArguments()
		);
	}

}
