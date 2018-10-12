<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class SavedSearchesEmailTemplate extends AbstractMigration
{

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        // Create users_emails table
        $this->table('users_emails', ['id' => false, 'primary_key' => 'guid'])
            ->addColumn('guid', 'binary', ['length' => 16, 'null' => false, 'default' => ''])
            ->addColumn('event_id', 'integer')
            ->create();

        // Add saved_searches_responder to rewidx_system table
        $rewidx_system = $this->table('rewidx_system');
        $rewidx_system->addColumn('savedsearches_responsive', 'enum', ['values' => ['false','true'], 'default' => 'false'])
            ->addColumn('savedsearches_responsive_params', 'text', ['limit' => MysqlAdapter::TEXT_LONG])
            ->update();

        // Default Responsive Template Params
        $default_params = [
            "sender" => [
                "from" => "agent",
                "name" => "",
                "email" => ""
            ],
            "message" => [
                "display" => "true",
                "subject" => "{date} - Your search {search_title} has some new listings!",
                "body" => "Hello {first_name},<br /><br />There are <strong>{result_count} new properties</strong> for the <em>{search_title}</em> search you saved on {site_link}",
            ],
            "listings" => [
                "num_rows" => "5"
            ],
            "agent" => [
                "display" => "true"
            ],
            "social_media" => [
                "from" => ""
            ],
            "mailing_address" => [
                "office_id" => ""
            ]
        ];

        $default_params = serialize($default_params);

        $this->execute("UPDATE rewidx_system SET savedsearches_responsive_params = '$default_params';");
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        // Drop users_emails table
        $this->dropTable('users_emails');

        // Remove saved_searches_responder from rewidx_system
        $rewidx_system = $this->table('rewidx_system');
        $rewidx_system->removeColumn('savedsearches_responsive')
            ->removeColumn('savedsearches_responsive_params')
            ->update();


    }

}