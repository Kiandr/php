<?php

use REW\Phinx\Migration\AbstractMigration;

class DefaultApi extends AbstractMigration
{
    /**
     * Create default application api if none exists
     * @return void
     */
    public function up()
    {

        $apiKey = $this->fetchRow("SELECT `api_key` FROM `api_applications` WHERE `id` = 1;");
        if (empty($apiKey)) {
            // Random key
            $apiTable = $this->table('api_applications');
            $apiTable->insert([
                'id'        => 1,
                'name'      => 'Default Application',
                'api_key'   => hash('sha256', uniqid('', true) . $_SERVER['HTTP_HOST']),
                'enabled'   => 'Y',
            ]);
            $apiTable->saveData();
        }
    }
}
