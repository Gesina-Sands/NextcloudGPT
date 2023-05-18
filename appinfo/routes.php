<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Shannon Sands <shannon.sands.1979@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\NextcloudGPT\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'resources' => [
		'note' => ['url' => '/notes'],
		'note_api' => ['url' => '/api/0.1/notes'],
		'openai_config' => ['url' => '/openai_configs'],
		'openai_config_api' => ['url' => '/api/0.1/openai_configs'],
		'message' => ['url' => '/messages'],
		'message_api' => ['url' => '/api/0.1/messages']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'note_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'openai_config_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'message_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'message#deleteAll', 'url' => '/delete-all', 'verb' => 'DELETE'],
		['name' => 'openai_config#upsert', 'url' => '/openai_configs', 'verb' => 'PUT'],
	]
];
