<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class LeadMessageCount extends AbstractMigration
{

    /**
     * Migrate Up
     * Create num_messages column on users table, update num_message values with messages already sent and
     * create trigger to increase num_messages whenever a message is create on users_messages table
     * @return void
     */
    public function up()
    {
        $users = $this->table('users');
        $users->addColumn('num_messages', 'integer', [
            'limit'   => MysqlAdapter::INT_REGULAR,
            'signed'  => false,
            'default' => 0,
            'after'   => 'num_rt_properties',
            'comment' => 'This field is updated through the `users_messages`.`upd_num_messages` trigger.'
        ])->update();

        $this->execute("UPDATE `users` `u` JOIN (SELECT `user_id`, COUNT(*) as `count` from `users_messages` WHERE `sent_from` = 'lead' GROUP BY `user_id`) `m` on `m`.`user_id` = `u`.`id` SET `u`.`num_messages` = `m`.`count`;");

        $this->execute("CREATE TRIGGER `upd_num_messages` BEFORE INSERT ON `users_messages`
                        FOR EACH ROW
                        BEGIN
                          IF NEW.`sent_from` = 'lead' THEN
                            UPDATE `users` SET `num_messages` = `num_messages` + 1 WHERE `id` = NEW.`user_id`;
                          END IF;
                        END;");
    }

    /**
     * Migrate Down
     * Drop upd_num_messages trigger and num_messages column
     * @return void
     */
    public function down()
    {
        $this->execute("DROP TRIGGER `upd_num_messages`;");

        $users = $this->table('users');
        $users->removeColumn('num_messages')->update();
    }

}