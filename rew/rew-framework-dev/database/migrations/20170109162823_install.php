<?php

use REW\Phinx\Migration\AbstractMigration;

class Install extends AbstractMigration
{

    public function up()
    {
        if ($this->hasTable('auth')) {
            return;
        }
        $fixture = sprintf('%s/fixtures/%s_up.sql', __DIR__, basename(__FILE__, '.php'));
        $this->execute(file_get_contents($fixture));
    }

    public function down()
    {
        $fixture = sprintf('%s/fixtures/%s_down.sql', __DIR__, basename(__FILE__, '.php'));
        $this->execute(file_get_contents($fixture));
    }
}
