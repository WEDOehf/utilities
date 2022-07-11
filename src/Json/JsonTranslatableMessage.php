<?php declare (strict_types = 1);

namespace Wedo\Utilities\Json;

use JsonSerializable;
use Nette\Localization\ITranslator;

class JsonTranslatableMessage implements JsonSerializable
{

	public string $key;

	/** @var mixed[] */
	public array $params = [];

	private ITranslator $translator;

	/**
	 * @param mixed[] $params
	 */
	public function __construct(string $key, array $params = [])
	{
		$this->key = $key;
		$this->params = $params;
	}

	public function jsonSerialize(): string
	{
		/** @var mixed[] $params */
		$params = array_merge([$this->key], $this->params);

		/** @var string $result */
		$result = call_user_func_array([$this->translator, 'translate'], $params);

		return $result;
	}

	public function setTranslator(ITranslator $translator): void
	{
		$this->translator = $translator;
	}

}
