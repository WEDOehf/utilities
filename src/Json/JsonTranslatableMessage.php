<?php declare (strict_types = 1);

namespace Wedo\Utilities\Json;

use JsonSerializable;
use Nette\Localization\ITranslator;

class JsonTranslatableMessage implements JsonSerializable
{

	private ITranslator $translator;

	public string $key;

	/** @var mixed[] */
	public array $params = [];

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

		return call_user_func_array([$this->translator, 'translate'], $params);
	}

	public function setTranslator(ITranslator $translator): void
	{
		$this->translator = $translator;
	}

}
