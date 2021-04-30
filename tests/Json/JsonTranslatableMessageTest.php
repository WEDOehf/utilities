<?php declare (strict_types = 1);

namespace Wedo\Utilities\Tests\Json;

use Nette\Localization\ITranslator;
use PHPUnit\Framework\TestCase;
use Wedo\Utilities\Json\JsonTranslatableMessage;

class JsonTranslatableMessageTest extends TestCase
{

	public function testJsonTranslatableMessage(): void
	{
		$message = new JsonTranslatableMessage('%s you %d', ['hey', 2]);
		$message->setTranslator(new class implements ITranslator {

			public function translate(mixed $message, mixed ...$parameters): string
			{
				return vsprintf($message, $parameters);
			}

		});

		$result = $message->jsonSerialize();
		$this->assertEquals('hey you 2', $result);
	}

}
