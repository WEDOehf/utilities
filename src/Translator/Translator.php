<?php declare(strict_types = 1);

namespace Wedo\Utilities\Translator;

use InvalidArgumentException;
use Nette\Localization\ITranslator;
use Psr\Log\LoggerInterface;

class Translator implements ITranslator
{

	private ITranslatorRepository $repository;

	private string $module;

	private string $language;

	private bool $insertMissing = true;

	/** @var array<string, array<string, string>> */
	private array $translations = [];

	private LoggerInterface $logger;

	public function __construct(ITranslatorRepository $repository, LoggerInterface $logger)
	{
		$this->repository = $repository;
		$this->logger = $logger;
	}

	/**
	 * Translates the given string.
	 *
	 * @throws InvalidArgumentException
	 */
	public function translate(mixed $key, mixed ...$parameters): string
	{
		if (!isset($this->module) || !isset($this->language)) {
			throw new InvalidArgumentException('Language and/or module not set!');
		}

		$key = (string) $key; //@phpstan-ignore-line

		if ($key === '') {
			return '';
		}

		$this->fillTranslations();

		$message = $this->getMessage($key);

		if ($message === '') {
			return $key;
		}

		$args = func_get_args();
		$message = $this->formatMessage($message, $args);

		return $message;
	}

	public function getModule(): string
	{
		return $this->module;
	}

	public function setModule(string $module): void
	{
		$this->module = $module;
	}

	public function getLanguage(): string
	{
		return $this->language;
	}

	public function setLanguage(string $language): void
	{
		$this->language = $language;
	}

	public function isInsertMissing(): bool
	{
		return $this->insertMissing;
	}

	public function setInsertMissing(bool $insertMissing = true): void
	{
		$this->insertMissing = $insertMissing;
	}

	public function getLanguageKey(): string
	{
		return $this->getLanguage() . '-' . $this->getModule();
	}

	/**
	 * int|array
	 *
	 * @param mixed[] $args
	 */
	protected function formatMessage(string $message, ?array $args): string
	{
		if ($args !== null && count($args) > 1) {
			$arr = $args[1];

			if (!is_array($arr)) {
				array_shift($args);
				$arr = $args;
			}

			$message = vsprintf($message, $arr);

			return $message;
		}

		return $message;
	}

	private function insertMissingKey(string $key): void
	{
		if ($this->isInsertMissing() && (!array_key_exists($key, $this->translations[$this->getLanguageKey()]))) {
			$this->repository->addKey($this->module, $this->language, $key);
			$this->translations[$this->getLanguageKey()][$key] = null; //@phpstan-ignore-line

			$this->logger->info('adding new key', ['key' => $key, 'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 20)]);
		}
	}

	/**
	 * Fills translation field
	 */
	private function fillTranslations(): void
	{
		if (!isset($this->translations[$this->getLanguageKey()])) {
			$this->translations[$this->getLanguageKey()] = $this->repository->getTranslations($this->getModule(), $this->getLanguage());
		}
	}

	private function getMessage(string $key): string
	{
		$lowerKey = strtolower($key);

		if (isset($this->translations[$this->getLanguageKey()][$lowerKey])) {
			return $this->translations[$this->getLanguageKey()][$lowerKey];
		}

		$this->insertMissingKey($lowerKey);

		return $key;
	}

}
