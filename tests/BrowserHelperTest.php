<?php declare(strict_types = 1);

namespace Wedo\Utilities\Tests;

use PHPUnit\Framework\TestCase;
use Wedo\Utilities\BrowserHelper;

class BrowserHelperTest extends TestCase
{

	public function testIsDesktop_ShouldReturnTrue(): void
	{
		$this->assertTrue(BrowserHelper::isDesktop(null));
		$this->assertTrue(BrowserHelper::isDesktop('Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0'));
	}

	public function testIsDesktop_ShouldReturnFalse(): void
	{
		$this->assertFalse(BrowserHelper::isDesktop(
			'Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30
			 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1'
		));
	}

}
