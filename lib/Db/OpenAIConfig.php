<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Shannon Sands <shannon.sands.1979@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\NextcloudGPT\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 * @method getModel(): string
 * @method setModel(string $model): void
 * @method getKey(): string
 * @method setKey(string $key): void
 * @method getUserId(): string
 * @method setUserId(string $userId): void
 */
class OpenAIConfig extends Entity implements JsonSerializable {
	protected string $model = '';
	protected string $key = '';
	protected string $userId = '';

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'model' => $this->title,
			'key' => $this->content
		];
	}
}
