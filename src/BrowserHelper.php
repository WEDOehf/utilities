<?php declare(strict_types = 1);

namespace Wedo\Utilities;

use Nette\Utils\Strings;

class BrowserHelper
{

	public static function isDesktop(?string $userAgent): bool
	{
		if ($userAgent === null) {
			return true;
		}

		$ua = strtolower($userAgent);

		return !(Strings::contains($ua, 'android') || Strings::contains($ua, 'iphone') || Strings::contains($ua, 'ipad'));
	}

}
