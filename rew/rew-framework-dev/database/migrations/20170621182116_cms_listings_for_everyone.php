<?php

use REW\Phinx\Migration\AbstractMigration;

class CmsListingsForEveryone extends AbstractMigration
{
    /**
     * Up Method.
     */
    public function up()
    {
        $count = $this->execute(
            "UPDATE `snippets`
            SET `agent` = NULL
            WHERE `name` = 'cms-listings';"
        );
    }

    /**
     * Down Method.
     */
    public function down()
    {
        $count = $this->execute(
            "UPDATE `snippets`
            SET `agent` = 1
            WHERE `name` = 'cms-listings';"
        );
    }
}
