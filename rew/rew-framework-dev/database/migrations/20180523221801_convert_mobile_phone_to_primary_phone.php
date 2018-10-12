<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class ConvertMobilePhoneToPrimaryPhone extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $this->execute(
            "UPDATE `users`
                SET `phone` = `phone_cell`,
                `phone_cell` = '',
                `phone_home_status` = `phone_cell_status`,
                `phone_cell_status` = ''
                WHERE `phone` IS NULL 
                OR `phone` = '';"
        );
    }

}