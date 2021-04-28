<?php declare(strict_types = 1);

namespace Wedo\Utilities\Aop;

use Contributte\Aop\Annotations\Around;
use Contributte\Aop\JoinPoint\AroundMethod;
use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\SmartObject;
use Nette\Utils\ArrayHash;
use Wedo\Utilities\Aop\Annotations\Cached;
use Wedo\Utilities\Aop\Annotations\FileCached;
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


	/**
	 * @Around("class(Wedo\Utilities\Aop\Markers\ICached) && methodAnnotatedWith(Wedo\Utilities\Aop\Annotations\Cached)")
	 * @return mixed
	 */
	public function cache(AroundMethod $method)
	{
		$cache_key = $this->getKey($method);

		$result = $this->cache->load($cache_key);

		if ($result === null) {
			$target = $method->getTargetReflection();
			$result = $method->proceed();

			/** @var Cached $annotation */
			$annotation = $target->getAnnotation('Cached');
			$seconds = $annotation->seconds ?? 60 * 15;
			$this->cache->save($cache_key, $result, [Cache::EXPIRATION => time() + $seconds]);
		}

		return $result;
	}


	/**
	 * @Around("class(Wedo\Utilities\Aop\Markers\ICached) && methodAnnotatedWith(Wedo\Utilities\Aop\Annotations\MemoryCached)")
	 * @return mixed
	 */
	public function memoryCache(AroundMethod $method)
	{
		$cache_key = $this->getKey($method);

		if (isset($this->mCache[$cache_key])) {
			return $this->mCache[$cache_key];
		}

		$this->mCache[$cache_key] = $method->proceed();

		return $this->mCache[$cache_key];
	}

	/**
	 * @Around("class(Wedo\Utilities\Aop\Markers\ICached) && methodAnnotatedWith(Wedo\Utilities\Aop\Annotations\FileCached)")
	 * @return mixed
	 */
	public function fileCache(AroundMethod $method)
	{
		$cache_key = $this->getKey($method);
		$result = $this->fileStorage->load($cache_key);

		if ($result === null) {
			$target = $method->getTargetReflection();
			$result = $method->proceed();

			/** @var FileCached|ArrayHash $annotation */
			$annotation = $target->getAnnotation('FileCached');

			$seconds = $annotation->seconds ?? 60 * 15;

			$this->fileStorage->save($cache_key, $result, [Cache::EXPIRATION => time() + $seconds]);
		}

		return $result;
	}

	protected function getKey(AroundMethod $method): string
	{
		/** @var Cached|ArrayHash $annotation */
		$annotation = $method->getTargetReflection()->getAnnotation('Cached');

		if (isset($annotation->key) && $annotation->key !== null) {
			$key = $annotation->key;

			return $key . CacheHelper::generateKeyFromParams($method->getArguments());
		}

		return CacheHelper::generateKey(
			$method->getTargetObject(),
			$method->getTargetReflection()->getName(),
			$method->getArguments()
		);
	}

}
