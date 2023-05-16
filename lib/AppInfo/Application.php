<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Shannon Sands <shannon.sands.1979@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NextcloudGPT\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'nextcloudgpt';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
