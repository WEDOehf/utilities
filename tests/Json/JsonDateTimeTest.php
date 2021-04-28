<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests\Json;

use PHPUnit\Framework\TestCase;
use Wedo\Utilities\Json\JsonDateTime;

class JsonDateTimeTest extends TestCase
{

	public function testIsToday_ShouldReturnTrue(): void
	{
		$jsonDateTime = new JsonDateTime();
		$this->assertTrue($jsonDateTime->isToday());
	}


	public function testIsToday_ShouldReturnFalse(): void
	{
		$jsonDateTime = new JsonDateTime('today - 2 day');
		$this->assertFalse($jsonDateTime->isToday());
	}


	public function testIsTomorrow_ShouldReturnTrue(): void
	{
		$jsonDateTime = new JsonDateTime('tomorrow');
		$this->assertTrue($jsonDateTime->isTomorrow());
	}


	public function testIsTomorrow_ShouldReturnFalse(): void
	{
		$jsonDateTime = new JsonDateTime();
		$this->assertFalse($jsonDateTime->isTomorrow());
	}

}
