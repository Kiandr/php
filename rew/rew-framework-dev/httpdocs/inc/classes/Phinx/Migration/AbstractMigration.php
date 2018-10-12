<?php

namespace REW\Phinx\Migration;

use Phinx\Migration\AbstractMigration as PhinxAbstractMigration;
use REW\Core\Interfaces\ContainerInterface;
use Container;

class AbstractMigration extends PhinxAbstractMigration
{

    /**
     * The location of the default migration template.
     * @const string
     */
    const MIGRATION_TEMPLATE = __DIR__ . '/Migration.template.php.dist';

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        if (!isset($this->container)) {
            $this->container = Container::getInstance();
        }
        return $this->container;
    }

    /**
     * A short-hand method to drop the given database table.
     * @deprecated since 0.10.0. Use $this->table($tableName)->drop()->save() instead.
     * @param string $tableName Table Name
     * @return void
     */
    public function dropTable($tableName)
    {
        $this->table($tableName)->drop()->save();
    }

    /**
     * Removed from PhinxAbstractMigration since v0.10.2
     * @see https://github.com/cakephp/phinx/releases/tag/v0.10.2
     * @return void
     */
    public function up()
    {
        return;
    }

    /**
     * Removed from PhinxAbstractMigration since v0.10.2
     * @see https://github.com/cakephp/phinx/releases/tag/v0.10.2
     * @return void
     */
    public function down()
    {
        return;
    }
}
