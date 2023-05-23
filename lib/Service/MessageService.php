<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Service;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\NextcloudGPT\Db\Message;
use OCA\NextcloudGPT\Db\MessageMapper;
use OCA\NextcloudGPT\Db\OpenAIConfigMapper;

class MessageService {
    private MessageMapper $mapper;

    public function __construct(
		MessageMapper $mapper,
		OpenAIConfigMapper $configMapper,
		SystemPromptService $promptService
		) {
        $this->mapper = $mapper;
		$this->configMapper = $configMapper;
		$this->promptService = $promptService;
    }

    /**
     * @return list<Message>
     */
    public function findAll(): array {
        return $this->mapper->findAll();
    }

    private function handleException(Exception $e) {
        if ($e instanceof DoesNotExistException ||
            $e instanceof MultipleObjectsReturnedException) {
            throw new MessageNotFound($e->getMessage());
        } else {
            throw $e;
        }
    }

    public function find(int $id): Message {
        try {
            return $this->mapper->find($id);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

	/**
	 * @ return list<Message>
	 */
    public function create(string $message, string $role) {
		$msg = new Message();
		$msg->setMessage($message);
		$msg->setRole($role);
		$user_message = $this->mapper->insert($msg);

		try {
			// Fetch the OpenAI config
			$config = $this->configMapper->findAll()[0]; // take the first one
			$apiKey = $config->getApiKey();
			$model = $config->getSelectedModel();

			$system_prompt = $this->promptService->findAll()[0]; // take the first one
			$system_prompt = $system_prompt->getPrompt();

			// If the system prompt is empty, fallback to default
			if (empty($system_prompt)) {
				$system_prompt = "The following is a conversation with an AI assistant. The assistant is helpful, creative, clever, and very friendly.";
			}

			$messages = array(
				array(
					'role' => 'system',
					'content' => $system_prompt),
			);

			// Fetch the last 10 messages and add them to the messages array
			$last_messages = array_slice($this->findAll(), -10);
			foreach($last_messages as $msg) {
				array_push($messages, array('role' => $msg->getRole(), 'content' => $msg->getMessage()));
			}

			// Add the current user message
			array_push($messages, array('role' => 'user', 'content' => $message));

			$result = $this->callOpenAI($apiKey, $model, $messages);
			$bot_msg = new Message();
			$bot_msg->setMessage($result['choices'][0]['message']['content']); // Set the assistant's message
			$bot_msg->setRole("assistant");
			$bot_msg = $this->mapper->insert($bot_msg);
			return [$user_message, $bot_msg];
		} catch(Exception $e){
			echo 'Error: ' .$e->getMessage();
		}
	}


    public function update(int $id, string $message, string $role): Message {
        try {
            $msg = $this->mapper->find($id);
            $msg->setMessage($message);
            $msg->setRole($role);
            return $this->mapper->update($msg);
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    public function delete(int $id): Message {
        try {
            $msg = $this->mapper->find($id);
            $this->mapper->delete($msg);
            return $msg;
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

	function callOpenAI($apiKey, $model, $messages){
		$url = 'https://api.openai.com/v1/chat/completions';

		$headers = array(
			'Content-Type: application/json',
			'Authorization: Bearer ' . $apiKey
		);

		$postData = array(
			'model' => $model,
			'messages' => $messages
		);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));

		$response = curl_exec($ch);

		if($response === false){
			throw new Exception('Curl error: ' . curl_error($ch));
		}

		curl_close($ch);

		return json_decode($response, true);
	}

	public function deleteAll() {
		return $this->mapper->deleteAll();
	}

}
