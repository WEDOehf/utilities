<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests\Json;

use DateTime;
use Nette\Utils\ArrayHash;
use PHPUnit\Framework\TestCase;
use Wedo\Utilities\Json\JsonDateTime;

class JsonObjectTest extends TestCase
{

	public function testFromToJson(): void
	{
		$this->assertNull(TestJson::fromJson(null));
		$json = '{"id":"15","name":"test","Created_date":null,"ignored":null}';
		/** @var TestJson $testJson */
		$testJson = TestJson::fromJson($json, true);
		$this->assertInstanceOf(TestJson::class, $testJson);
		$this->assertEquals(15, $testJson->id);
		$this->assertEquals('test', $testJson->name);

		$this->assertEquals('{"id":15,"name":"test","created_date":null,"ignored":null}', $testJson->toJson());
	}

	public function testCreateFromRow(): void
	{
		$row = ArrayHash::from([
			'id' => 2,
			'name' => 'test',
			'created' => new DateTime('2018-01-01'),
			'should_not_exist' => 'test',
			'bla' => null,
			'ignored' => '123',
			]);
		/** @var TestJson $testJson */
		$testJson = TestJson::createFromRow($row);
		$this->assertInstanceOf(TestJson::class, $testJson);
		$this->assertEquals(2, $testJson->id);
		$this->assertEquals('test', $testJson->name);
		$this->assertInstanceOf(JsonDateTime::class, $testJson->created_date);
		$this->assertFalse(isset($testJson->created));
		$this->assertEquals('{"id":2,"name":"test","created_date":"2018-01-01T00:00:00+00:00"}', $testJson->toJson());
		$this->assertEquals('{"id":2,"name":"test","created_date":"2018-01-01T00:00:00+00:00"}', json_encode($testJson));
	}

	public function testCreateArrayFromRows(): void
	{
		$rows = [
			ArrayHash::from(['id' => 1, 'name' => 'test1', 'created' => null]),
			ArrayHash::from(['id' => 2, 'name' => 'test2', 'created' => null]),
		];
		$now = new JsonDateTime();
		$result = TestJson::createArrayFromRows($rows, null, fn (TestJson $entity, $row) => $entity->created_date = $now);
		$this->assertCount(2, $result);
		$this->assertEquals($result[0]->id, 1);
		$this->assertEquals($result[0]->created_date, $now);
		$this->assertEquals($result[1]->id, 2);
	}

	public function testCreateArrayFromRows_WithNull_ShouldReturnEmptyArray(): void
	{
		$result = TestJson::createArrayFromRows(null);
		$this->assertEquals([], $result);
	}

	public function testFromJsonNull(): void
	{
		$this->assertNull(TestJson::fromJson('null'));
	}

}
