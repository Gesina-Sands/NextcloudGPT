<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Controller;

use OCA\NextcloudGPT\AppInfo\Application;
use OCA\NextcloudGPT\Service\OpenAIConfigService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class OpenAIConfigApiController extends ApiController {
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
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function index(): DataResponse {
        return new DataResponse($this->service->findAll($this->userId));
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function show(int $id): DataResponse {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->find($id, $this->userId);
        });
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function create(string $apiKey, string $selectedModel, float $topP, int $frequencyPenalty, int $maxLength, int $presencePenalty, int $tokenLength): DataResponse {
        return new DataResponse($this->service->create($apiKey, $selectedModel, $topP, $frequencyPenalty, $maxLength, $presencePenalty, $tokenLength, $this->userId));
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function update(int $id, string $apiKey, string $selectedModel, float $topP, int $frequencyPenalty, int $maxLength, int $presencePenalty, int $tokenLength): DataResponse {
        return $this->handleNotFound(function () use ($id, $apiKey, $selectedModel, $topP, $frequencyPenalty, $maxLength, $presencePenalty, $tokenLength) {
            return $this->service->upsert($id, $apiKey, $selectedModel, $topP, $frequencyPenalty, $maxLength, $presencePenalty, $tokenLength, $this->userId);
        });
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function destroy(int $id): DataResponse {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->delete($id, $this->userId);
        });
    }
}
