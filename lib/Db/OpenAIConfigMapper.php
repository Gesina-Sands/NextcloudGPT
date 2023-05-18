<?php
declare(strict_types=1);

namespace OCA\NextcloudGPT\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\MultipleObjectsReturnedException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<OpenAIConfig>
 */
class OpenAIConfigMapper extends QBMapper {
    public function __construct(IDBConnection $db) {
        parent::__construct($db, 'openai_api_configs', OpenAIConfig::class);
    }

    /**
     * @throws DoesNotExistException
     * @throws MultipleObjectsReturnedException
     */
    public function find(): OpenAIConfig {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('openai_api_configs');
        return $this->findEntity($qb);
    }

    /**
     * @param string $userId
     * @return array
     */
    public function findAll(): array {
        /* @var $qb IQueryBuilder */
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('openai_api_configs');
        return $this->findEntities($qb);
    }
}
