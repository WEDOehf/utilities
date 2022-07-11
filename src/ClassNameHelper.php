<?php declare (strict_types = 1);

namespace Wedo\Utilities;

use Nette\Utils\Reflection;
use Nette\Utils\Strings;
use ReflectionClass;
use ReflectionException;

class ClassNameHelper
{

	/**
	 * @throws ReflectionException
	 */
	public static function extractFqnFromObjectUseStatements(object $object, string $shortClassName): ?string
	{
		if (class_exists($shortClassName)) {
			return $shortClassName;
		}

		$reflectionClass = new ReflectionClass($object);
		$classInSameNamespace = $reflectionClass->getNamespaceName() . '\\' . $shortClassName;

		if (class_exists($classInSameNamespace)) {
			return $classInSameNamespace;
		}

		$uses = Reflection::getUseStatements($reflectionClass);

		foreach ($uses as $use) {
			if ($shortClassName === self::getShortClassName($use)) {
				return $use;
			}
		}

		return $shortClassName;
	}

	public static function getShortClassName(string $class): string
	{
		$classType = Strings::after($class, '\\', -1);

		if ($classType === null) {
			return $class;
		}

		return $classType;
	}

}
