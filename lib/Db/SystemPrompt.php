<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method int getId()
 * @method string getPrompt()
 * @method void setPrompt(string $prompt)
 */
class SystemPrompt extends Entity implements JsonSerializable {
    protected string $prompt = '';

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'prompt' => $this->prompt,
        ];
    }
}
