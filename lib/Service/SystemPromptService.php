<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Service;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\NextcloudGPT\Db\SystemPrompt;
use OCA\NextcloudGPT\Db\SystemPromptMapper;

class SystemPromptService {
    private SystemPromptMapper $mapper;

    public function __construct(SystemPromptMapper $mapper) {
        $this->mapper = $mapper;
    }

    /**
     * @return list<SystemPrompt>
     */
    public function findAll(): array {
        return $this->mapper->findAll();
    }

    private function handleException(Exception $e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new SystemPromptNotFound($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find(int $id): SystemPrompt {
        try {
            return $this->mapper->find($id);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function create(string $prompt): SystemPrompt {
        $sys_prompt = new SystemPrompt();
        $sys_prompt->setPrompt($prompt);
        return $this->mapper->insert($sys_prompt);
    }

    public function update(int $id, string $prompt): SystemPrompt {
        try {
            $sys_prompt = $this->mapper->find($id);
            $sys_prompt->setPrompt($prompt);
            return $this->mapper->update($sys_prompt);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

	public function upsert(string $prompt): SystemPrompt {
		try {
			// try to find existing system prompt
			$sys_prompt = $this->findAll()[0];
		} catch (SystemPromptNotFound $e) {
			// if not found, create a new one
			$sys_prompt = new SystemPrompt();
		}
		if ($sys_prompt === null) {
			$sys_prompt = new SystemPrompt();
		}

		$sys_prompt->setPrompt($prompt);

		if ($sys_prompt->getId() === null) {
			// if id is null, insert
			return $this->mapper->insert($sys_prompt);
		} else {
			// if id is not null, update
			return $this->mapper->update($sys_prompt);
		}
	}

    public function delete(int $id): SystemPrompt {
        try {
            $sys_prompt = $this->mapper->find($id);
            $this->mapper->delete($sys_prompt);
            return $sys_prompt;
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function deleteAll() {
        return $this->mapper->deleteAll();
    }

}
