<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Service;

use Exception;
use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCA\NextcloudGPT\Db\Message;
use OCA\NextcloudGPT\Db\MessageMapper;

class MessageService {
    private MessageMapper $mapper;

    public function __construct(MessageMapper $mapper) {
        $this->mapper = $mapper;
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
		// 	// 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
			$apiKey = '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';
			$model = 'gpt-3.5-turbo';
			$messages = array(
				array('role' => 'system', 'content' => 'You are a helpful chatbot next inside the Nextcloud platform'),
				array('role' => 'user', 'content' => $message)
			);

			$result = $this->callOpenAI($apiKey, $model, $messages);
			$bot_msg = new Message();
			$bot_msg->setMessage($result['choices'][0]['message']['content']); // Set the assistant's message
			$bot_msg->setRole("assistant");
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

}
