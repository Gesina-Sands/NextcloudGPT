<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<SystemPrompt>
 */
class SystemPromptMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'system_prompts', SystemPrompt::class);
    }

    /**
     * @param int $id
     * @return SystemPrompt
     * @throws \OCP\AppFramework\Db\DoesNotExistException
     * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
     */
    public function find(int $id): SystemPrompt {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('system_prompts')
            ->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
        return $this->findEntity($qb);
    }

    /**
     * @return array<SystemPrompt>
     */
    public function findAll(): array {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('system_prompts');
        return $this->findEntities($qb);
    }
}
