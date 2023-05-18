<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Controller;

use OCA\NextcloudGPT\AppInfo\Application;
use OCA\NextcloudGPT\Service\OpenAIConfigService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class OpenaiConfigController extends Controller {
    private OpenAIConfigService $service;
    private ?string $userId;

    use Errors;

    public function __construct(IRequest $request,
                                OpenAIConfigService $service,
                                ?string $userId) {
        parent::__construct(Application::APP_ID, $request);
        $this->service = $service;
        $this->userId = $userId;
    }

    /**
     * @NoAdminRequired
	 * @NoCSRFRequired
     */
    public function index(): DataResponse {
        return new DataResponse($this->service->findAll($this->userId));
    }

    /**
     * @NoAdminRequired
	 * @NoCSRFRequired
     */
    public function upsert(string $apiKey, string $selectedModel, float $topP, int $frequencyPenalty, int $maxLength, int $presencePenalty, int $tokenLength): DataResponse {
        return $this->handleNotFound(function () use ($apiKey, $selectedModel, $topP, $frequencyPenalty, $maxLength, $presencePenalty, $tokenLength) {
            return $this->service->upsert($apiKey, $selectedModel, $topP, $frequencyPenalty, $maxLength, $presencePenalty, $tokenLength, $this->userId);
        });
    }
}
