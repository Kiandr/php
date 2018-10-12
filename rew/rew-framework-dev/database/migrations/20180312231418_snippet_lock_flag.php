<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class SnippetLockFlag extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['SNIPPETS'];
        $table = $this->table($tableName);

        $table->addColumn('locked', 'enum', ['values' => ['false','true'], 'default' => 'false', 'comment' => 'Flag for lock snippet', 'null' => false])
            ->update();

        $this->execute( "UPDATE `snippets` SET `locked` = 'true' WHERE `name` IN('site-logo', 'site-logo-link');" );
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['SNIPPETS'];
        $table = $this->table($tableName);

        $table->removeColumn('locked')
            ->update();
    }

}