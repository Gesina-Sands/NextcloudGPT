<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Controller;

use OCA\NextcloudGPT\AppInfo\Application;
use OCA\NextcloudGPT\Service\SystemPromptService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class SystemPromptController extends Controller {
    private SystemPromptService $service;
    private ?string $userId;

    use Errors;

    public function __construct(IRequest $request,
                                SystemPromptService $service,
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
        return new DataResponse($this->service->findAll());
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function show(int $id): DataResponse {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->find($id);
        });
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function create(string $prompt): DataResponse {
        return new DataResponse($this->service->create($prompt));
    }

	/**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function upsert(string $prompt): DataResponse {
        return new DataResponse($this->service->upsert($prompt));
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function update(int $id, string $prompt): DataResponse {
        return $this->handleNotFound(function () use ($id, $prompt) {
            return $this->service->update($id, $prompt);
        });
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function destroy(int $id): DataResponse {
        return $this->handleNotFound(function () use ($id) {
            return $this->service->delete($id);
        });
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function deleteAll(): DataResponse {
        return new DataResponse($this->service->deleteAll());
    }
}
