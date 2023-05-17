<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2023 Your name <your@email.com>
 *
 * @author Your name <your@email.com>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\NextcloudGPT\Migration;

use Closure;
use OCP\DB\ISchemaWrapper;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

/**
 * Auto-generated migration step: Please modify to your needs!
 */
class Version100Date20230517095209 extends SimpleMigrationStep {

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function preSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 * @return null|ISchemaWrapper
	 */
	public function changeSchema(IOutput $output, Closure $schemaClosure, array $options) {
        /** @var ISchemaWrapper $schema */
        $schema = $schemaClosure();

        if (!$schema->hasTable('openai_api_configs')) {
            $table = $schema->createTable('openai_api_configs');
            $table->addColumn('id', 'integer', [
                'autoincrement' => true,
                'notnull' => true,
            ]);
            $table->addColumn('api_key', 'string', [
                'notnull' => true,
                'length' => 200,
            ]);
            $table->addColumn('selected_model', 'string', [
                'notnull' => true,
                'length' => 20,
                'default' => 'gpt-4'
            ]);
            $table->addColumn('top_p', 'float', [
                'notnull' => true,
                'default' => 0.9
            ]);
            $table->addColumn('frequency_penalty', 'integer', [
                'notnull' => true,
                'default' => 0
            ]);
            $table->addColumn('max_length', 'integer', [
                'notnull' => true,
                'default' => 2048
            ]);
            $table->addColumn('presence_penalty', 'integer', [
                'notnull' => true,
                'default' => 0
            ]);
            $table->addColumn('token_length', 'integer', [
                'notnull' => true,
                'default' => 4096
            ]);

            $table->setPrimaryKey(['id']);
        }
        return $schema;
    }

	/**
	 * @param IOutput $output
	 * @param Closure(): ISchemaWrapper $schemaClosure
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options): void {
	}
}
