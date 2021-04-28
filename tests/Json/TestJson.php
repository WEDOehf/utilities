<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests\Json;

use Wedo\Utilities\Json\JsonDateTime;
use Wedo\Utilities\Json\JsonObject;

class TestJson extends JsonObject
{

	public int $id;

	public string $name;

	public ?JsonDateTime $created_date;

	public ?string $ignored;

	public ?string $none;

	/**
	 * @return array<string, string>
	 */
	protected static function getReplacementColumnsMapping(): array
	{
		return ['created_date' => 'created'];
	}


	/**
	 * @return string[]
	 */
	public static function skipColumns(): array
	{
		return ['ignored'];
	}

}
