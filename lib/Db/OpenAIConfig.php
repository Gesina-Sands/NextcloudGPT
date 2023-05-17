<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method int getId()
 * @method string getApiKey()
 * @method void setApiKey(string $apiKey)
 * @method string getSelectedModel()
 * @method void setSelectedModel(string $selectedModel)
 * @method float getTopP()
 * @method void setTopP(float $topP)
 * @method int getFrequencyPenalty()
 * @method void setFrequencyPenalty(int $frequencyPenalty)
 * @method int getMaxLength()
 * @method void setMaxLength(int $maxLength)
 * @method int getPresencePenalty()
 * @method void setPresencePenalty(int $presencePenalty)
 * @method int getTokenLength()
 * @method void setTokenLength(int $tokenLength)
 */
class OpenAIConfig extends Entity implements JsonSerializable {
    protected string $apiKey = '';
    protected string $selectedModel = '';
    protected float $topP = 0.9;
    protected int $frequencyPenalty = 0;
    protected int $maxLength = 2048;
    protected int $presencePenalty = 0;
    protected int $tokenLength = 4096;

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'apiKey' => $this->apiKey,
            'selectedModel' => $this->selectedModel,
            'topP' => $this->topP,
            'frequencyPenalty' => $this->frequencyPenalty,
            'maxLength' => $this->maxLength,
            'presencePenalty' => $this->presencePenalty,
            'tokenLength' => $this->tokenLength
        ];
    }
}
