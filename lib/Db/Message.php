<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Db;

use JsonSerializable;
use OCP\AppFramework\Db\Entity;

/**
 * @method int getId()
 * @method string getMessage()
 * @method void setMessage(string $message)
 * @method string getRole()
 * @method void setRole(string $role)
 * @method string getUserId()
 * @method void setUserId(string $userId)
 * @method \DateTime getCreatedAt()
 * @method void setCreatedAt(\DateTime $createdAt)
 */
class Message extends Entity implements JsonSerializable {
    protected string $message = '';
    protected string $role = '';
    protected string $userId = '';
    protected \DateTime $createdAt;

    public function jsonSerialize(): array {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'role' => $this->role,
            'userId' => $this->userId,
            'created_at' => $this->createdAt->format(\DateTime::ATOM)
        ];
    }
}
