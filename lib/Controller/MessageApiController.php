<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Controller;

use OCA\NextcloudGPT\AppInfo\Application;
use OCA\NextcloudGPT\Service\MessageService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class MessageApiController extends ApiController {
    private MessageService $service;
    private ?string $userId;

    use Errors;

    public function __construct(IRequest $request,
                                MessageService $service,
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
    public function create(string $message, string $role): DataResponse {
        return new DataResponse($this->service->create($message, $role,
            $this->userId));
    }

    /**
     * @CORS
     * @NoCSRFRequired
     * @NoAdminRequired
     */
    public function update(int $id, string $message, string $role): DataResponse {
        return $this->handleNotFound(function () use ($id, $message, $role) {
            return $this->service->update($id, $message, $role, $this->userId);
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
