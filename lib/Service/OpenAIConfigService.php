<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Service;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\NextcloudGPT\Db\OpenAIConfig;
use OCA\NextcloudGPT\Db\OpenAIConfigMapper;

class OpenAIConfigService {
    private OpenAIConfigMapper $mapper;

    public function __construct(OpenAIConfigMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * @return list<OpenAIConfig>
     */
    public function findAll(): array {
        return $this->mapper->findAll();
    }

    private function handleException(Exception $e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new OpenAIConfigNotFound($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find(int $id, string $userId): OpenAIConfig {
        try {
            return $this->mapper->find($id, $userId);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function create(string $apiKey, string $selectedModel, float $topP, int $frequencyPenalty, int $maxLength, int $presencePenalty, int $tokenLength, string $userId): OpenAIConfig {
        $config = new OpenAIConfig();
        $config->setApiKey($apiKey);
        $config->setSelectedModel($selectedModel);
        $config->setTopP($topP);
        $config->setFrequencyPenalty($frequencyPenalty);
        $config->setMaxLength($maxLength);
        $config->setPresencePenalty($presencePenalty);
        $config->setTokenLength($tokenLength);
        return $this->mapper->insert($config);
    }

    public function update(int $id, string $apiKey, string $selectedModel, float $topP, int $frequencyPenalty, int $maxLength, int $presencePenalty, int $tokenLength): OpenAIConfig {
        try {
            $config = $this->mapper->find($id);
            $config->setApiKey($apiKey);
            $config->setSelectedModel($selectedModel);
            $config->setTopP($topP);
            $config->setFrequencyPenalty($frequencyPenalty);
            $config->setMaxLength($maxLength);
            $config->setPresencePenalty($presencePenalty);
            $config->setTokenLength($tokenLength);
            return $this->mapper->update($config);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

	public function upsert(string $apiKey, string $selectedModel, float $topP, int $frequencyPenalty, int $maxLength, int $presencePenalty, int $tokenLength): OpenAIConfig {
        try {
            // try to find existing config
            $config = $this->findAll()[0];
        } catch (OpenAIConfigNotFound $e) {
            // if not found, create a new one
            $config = new OpenAIConfig();
        }
		if($config === null) {
			$config = new OpenAIConfig();
		}

        $config->setApiKey($apiKey);
        $config->setSelectedModel($selectedModel);
        $config->setTopP($topP);
        $config->setFrequencyPenalty($frequencyPenalty);
        $config->setMaxLength($maxLength);
        $config->setPresencePenalty($presencePenalty);
        $config->setTokenLength($tokenLength);

        if ($config->getId() === null) {
            // if id is null, insert
            return $this->mapper->insert($config);
        } else {
            // if id is not null, update
            return $this->mapper->update($config);
        }
    }

    public function delete(int $id): OpenAIConfig {
        try {
            $config = $this->mapper->find($id);
            $this->mapper->delete($config);
            return $config;
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
}
