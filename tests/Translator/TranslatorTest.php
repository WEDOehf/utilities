<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests\Translator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Wedo\Utilities\Translator\ITranslatorRepository;
use Wedo\Utilities\Translator\Translator;

class TranslatorTest extends TestCase
{

	private MockObject|ITranslatorRepository $repository;

	private MockObject|LoggerInterface $logger;

	/** @var string[] */
	private array $translations = [
		'key1' => 'translation',
		'key2' => 'translation with param %s',
		'empty-key' => null,
		'empty key with %s' => null,
		'whitespace-translation' => '',

	];

	public function testTranslate(): void
	{
		$this->repository->expects($this->once())->method('getTranslations')
			->with('module', 'language')->willReturn($this->translations);

		$translator = new Translator($this->repository, $this->logger);
		$translator->setModule('module');
		$translator->setLanguage('language');

		$this->assertEquals('', $translator->translate(''));
		$this->assertEquals($this->translations['key1'], $translator->translate('key1'));
		$this->assertEquals('translation with param bla', $translator->translate('key2', 'bla'));
		$this->assertEquals('empty-key', $translator->translate('empty-key'));
		$this->assertEquals('empty key with parameter', $translator->translate('empty key with %s', 'parameter'));

		$this->assertEquals('whitespace-translation', $translator->translate('whitespace-translation'));

		$this->repository->expects($this->once())->method('addKey')
			->with('module', 'language', 'translated %s %s');

		$this->assertEquals('translated 1 bla', $translator->translate('translated %s %s', 1, 'bla'));

		$translator->setInsertMissing(false);

		$this->repository->expects($this->never())->method('addKey')
			->with('missing-key-2');
		$translator->translate('missing-key-2');
	}

	public function testLanguageNotSet(): void
	{
		$this->expectException('InvalidArgumentException');
		$translator = new Translator($this->repository, $this->logger);
		$translator->setModule('cms');
		$translator->translate('anything');
	}

	public function testModuleNotSet(): void
	{
		$this->expectException('InvalidArgumentException');
		$translator = new Translator($this->repository, $this->logger);
		$translator->setLanguage('cms');
		$translator->translate('anything');
	}

	protected function setUp(): void
	{
		$this->repository = $this->createMock(ITranslatorRepository::class);
		$this->logger = $this->createMock(LoggerInterface::class);
	}

}
