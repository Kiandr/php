<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class WiseagentId extends AbstractMigration
{
    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $settings = Container::getInstance()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_LEADS'];
        $table = $this->table($tableName);

        $table->addColumn('wiseagent_id', 'string', ['limit' => 32])->update();

    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $settings = Container::getInstance()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_LEADS'];
        $table = $this->table($tableName);

        $table->removeColumn('wiseagent_id')->update();
    }

}
