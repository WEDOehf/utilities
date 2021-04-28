<?php declare(strict_types = 1);

namespace Wedo\Utilities\Translator;

interface ITranslatorRepository
{

	/**
	 * @return string[]
	 */
	public function getTranslations(string $module, string $language): array;


	/**
	 * Get language id by module and language
	 */
	public function getLanguageId(string $module, string $language): int;


	/**
	 * Adds key to database
	 */
	public function addKey(string $module, string $language, string $key): void;


	/**
	 * Loads language id from database by module and language
	 */
	public function getLanguageIdFromDatabase(string $module, string $language): int;


	/**
	 * Loads all translations from database by module and language
	 *
	 * @return string[]
	 */
	public function getTranslationsFromDatabase(string $module, string $language): array;

}
