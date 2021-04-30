<?php declare(strict_types = 1);

namespace Wedo\Utilities;

use Nette\Utils\Json;

class CacheHelper
{

	/**
	 * @param mixed[] $params
	 */
	public static function generateKey(object $obj, string $methodName = '', array $params = []): string
	{
		return 'MethodCache:' . $obj::class . ':' . $methodName . self::generateKeyFromParams($params);
	}

	/**
	 * @param mixed[] $params
	 */
	public static function generateKeyFromParams(array $params): string
	{
		$key = '';

		foreach ($params as $param) {
			if (!is_array($param) && !is_object($param)) {
				$key .= ':' . $param;
			} else {
				$key .= ':' . md5(Json::encode($param));
			}
		}

		return $key;
	}

}
