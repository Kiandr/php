<?php

use REW\Phinx\Migration\AbstractMigration;

class AddLeadUploadsFolder extends AbstractMigration
{

    /**
     * Create httpdocs/uploads/lead folder
     */
    public function change()
    {
        $folder = __DIR__ . '/../../httpdocs/uploads/leads';
        if (!is_dir($folder)) {
            mkdir($folder);
        }
    }
}
