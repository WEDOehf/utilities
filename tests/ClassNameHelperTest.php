<?php declare (strict_types = 1);

namespace Wedo\Utilities\Tests;

use PHPUnit\Framework\TestCase;
use stdClass;
use Wedo\Utilities\ClassNameHelper;

class ClassNameHelperTest extends TestCase
{

	public function testExtractFqnFromObjectUseStatements(): void
	{
		self::assertEquals('\stdClass', ClassNameHelper::extractFqnFromObjectUseStatements($this, '\stdClass'));
		self::assertEquals(ClassNameHelper::class, ClassNameHelper::extractFqnFromObjectUseStatements($this, ClassNameHelper::class));
		self::assertEquals(ClassNameHelper::class, ClassNameHelper::extractFqnFromObjectUseStatements($this, 'ClassNameHelper'));
		self::assertEquals(self::class, ClassNameHelper::extractFqnFromObjectUseStatements($this, 'ClassNameHelperTest'));
		self::assertEquals('int', ClassNameHelper::extractFqnFromObjectUseStatements($this, 'int'));
	}

	public function testGetShortClassName(): void
	{
		self::assertEquals('ClassNameHelper', ClassNameHelper::getShortClassName(ClassNameHelper::class));

		self::assertEquals('stdClass', ClassNameHelper::getShortClassName(stdClass::class));
	}

}
