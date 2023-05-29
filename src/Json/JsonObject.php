<?php //phpcs:ignore

namespace Wedo\Utilities\Json;

use DateTimeInterface;
use Nette\Utils\Json;

class JsonObject
{

	/**
	 * @return static
	 */
	public static function fromJson(?string $json = null, bool $firstLowerCase = false): ?self
	{
		if ($json === null) {
			return null;
		}

		/** @var array<int|string, mixed>|null $arr */
		$arr = json_decode($json, true);

		if ($arr === null) {
			return null;
		}

		return self::fromArray($arr, $firstLowerCase);
	}

	public static function arrayFromJson(string $json,?string $subproperty = null, bool $firstLowerCase = false): ?array
	{

		/** @var array<int|string, mixed>|null $arr */
		$arr = json_decode($json, true);

		if ($arr === null) {
			return null;
		}

		if ($subproperty !== null) {
			$arr = $arr[$subproperty];
		}

		$result = [];

		foreach ($arr as $row) {
			$result[] = self::fromArray($row, $firstLowerCase);
		}

		return $result;
	}

	/**
	 * Gets object from array, fills up only defined properties in class
	 *
	 * @param array<string|mixed, mixed> $arr
	 * @return static
	 * @internal use fromRow instead
	 */
	public static function fromArray(array $arr, bool $firstLowerCase = false): self
	{
		$class_name = static::class;
		$data = new $class_name();

		foreach ($arr as $key => $value) {
			if (is_string($key) && $firstLowerCase) {
				$key = lcfirst($key);
			}

			$data->{$key} = $value; //@phpstan-ignore-line
		}

		return $data;
	}

	/**
	 * @param array<string, string> $replacements
	 * @return static
	 */
	public static function createFromRow(object $row, ?array $replacements = null): self
	{
		$arr = method_exists($row, 'toArray') ? $row->toArray() : (array) $row;
		$class_name = static::class;
		$data = new $class_name();
		$fields = array_keys(get_class_vars($class_name), null, true);
		$replacements ??= static::getReplacementColumnsMapping();

		foreach ($fields as $field) {
			if (in_array($field, static::skipColumns(), true)) {
				continue;
			}

			$key = $replacements[$field] ?? $field;

			if (!array_key_exists($key, $arr)) {
				continue;
			}

			$value = $arr[$key];

			if ($value instanceof DateTimeInterface) {
				$value = new JsonDateTime($value);
			}

			$data->{$field} = $value; //@phpstan-ignore-line
		}

		static::rowCallback($data, $row);

		return $data;
	}

	/**
	 * returns object serialized to JSON
	 */
	public function toJson(): string
	{
		return Json::encode($this);
	}

	/**
	 * @param object[] $rows
	 * @param string[]              $replacements
	 * @param callable              $callback ($result, $row)
	 * @return static[]
	 */
	public static function createArrayFromRows(?array $rows, ?array $replacements = null, ?callable $callback = null): array
	{
		if ($rows === null) {
			return [];
		}

		$result = [];

		foreach ($rows as $row) {
			$entity = self::createFromRow($row, $replacements);

			if ($callback !== null) {
				call_user_func($callback, $entity, $row);
			}

			$result[] = $entity;
		}

		return $result;
	}

	/**
	 * @return array<string, string>
	 * @codeCoverageIgnore
	 */
	protected static function getReplacementColumnsMapping(): array
	{
		return [];
	}

	protected static function rowCallback(self $entity, object $row): void
	{
		//nothing to handle
	}

	/**
	 * @return string[]
	 */
	protected static function skipColumns(): array
	{
		return [];
	}

}
