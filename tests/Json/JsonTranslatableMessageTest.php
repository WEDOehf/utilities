<?php declare (strict_types = 1);

namespace Wedo\Utilities\Tests\Json;

use Nette\Localization\ITranslator;
use PHPUnit\Framework\TestCase;
use Wedo\Utilities\Json\JsonTranslatableMessage;

class JsonTranslatableMessageTest extends TestCase
{

	public function testJsonTranslatableMessage(): void
	{
		$message = new JsonTranslatableMessage('%s you %d', ['hey', 2 ]);
		$message->setTranslator(new class implements ITranslator {

			/**
			 * @param string|mixed $message
			 * @param mixed $parameters
			 */
			public function translate($message, ...$parameters): string
			{
				return vsprintf($message, $parameters);
			}

		});

		$result = $message->jsonSerialize();
		$this->assertEquals('hey you 2', $result);
	}

}
