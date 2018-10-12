<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class EnableDriveTimeMainDomain extends AbstractMigration
{

    /**
     * Enable drive time on main domain
     * @return void
     */
    public function up()
    {
        $this->execute("UPDATE `agents` SET `cms_addons` = 'drivetime' WHERE `id` = 1;");
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $this->execute("UPDATE `agents` SET `cms_addons` = '' WHERE `id` = 1;");
    }

}