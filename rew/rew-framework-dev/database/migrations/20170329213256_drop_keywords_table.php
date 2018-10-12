<?php

use REW\Phinx\Migration\AbstractMigration;

class DropKeywordsTable extends AbstractMigration
{

    /**
     * Drop `users_keywords` and `keywords` tables
     */
    public function up()
    {
        $this->dropTable('users_keywords');
        $this->dropTable('keywords');
    }
}
