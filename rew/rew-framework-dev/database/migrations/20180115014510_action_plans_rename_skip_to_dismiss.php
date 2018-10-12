<?php

use REW\Phinx\Migration\AbstractMigration;

class ActionPlansRenameSkipToDismiss extends AbstractMigration
{
    public function up()
    {
        $users_tasks = $this->table('users_tasks');

        // Temporarily support both values so we can accurately update row values from Skipped => Dismissed
        $users_tasks->changeColumn('status', 'enum', ['values' => ['Pending', 'Completed', 'Dismissed', 'Skipped', 'Expired']])
            ->save();

        // Update Skipped => Dismissed
        $this->execute("UPDATE `users_tasks` SET `status` = 'Dismissed' WHERE `status` = 'Skipped';");

        // Remove the excess "Skipped" value
        $users_tasks->changeColumn('status', 'enum', ['values' => ['Pending', 'Completed', 'Dismissed', 'Expired']])
            ->save();
    }

    public function down()
    {
        $users_tasks = $this->table('users_tasks');

        // Temporarily support both values so we can accurately revert row values from Dismissed => Skipped
        $users_tasks->changeColumn('status', 'enum', ['values' => ['Pending', 'Completed', 'Dismissed', 'Skipped', 'Expired']])
            ->save();

        // Update Dismissed => Skipped
        $this->execute("UPDATE `users_tasks` SET `status` = 'Skipped' WHERE `status` = 'Dismissed';");

        // Remove the excess "Dismissed" value
        $users_tasks->changeColumn('status', 'enum', ['values' => ['Pending', 'Completed', 'Skipped', 'Expired']])
            ->save();
    }
}
