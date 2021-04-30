<?php declare(strict_types = 1);

namespace Wedo\Utilities\Json;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use JsonSerializable;

class JsonDateTime extends DateTime implements JsonSerializable
{

	public function __construct(string|DateTimeInterface $time = 'now', ?DateTimeZone $timezone = null)
	{
		if ($time instanceof DateTimeInterface) {
			parent::__construct($time->format('Y-m-d H:i:s'), $timezone ?? $time->getTimezone());
		} else {
			parent::__construct($time, $timezone);
		}
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 */
	public function jsonSerialize(): mixed
	{
		return $this->format(DateTime::ATOM);
	}

	public function isToday(): bool
	{
		$now = new DateTime();

		return $now->format('Y-m-d') === $this->format('Y-m-d');
	}

	public function isTomorrow(): bool
	{
		$now = new DateTime();
		$diff = (new DateTime($now->format('Y-m-d')))->diff(new DateTime($this->format('Y-m-d')));

		return $diff->days === 1 && $diff->invert === 0;
	}

}
