<?php

use REW\Phinx\Migration\AbstractMigration;
use Phinx\Db\Adapter\MysqlAdapter;

class RemoveMetaKeywords extends AbstractMigration
{
    private $tables = [
        'default_info' => [
            'column' => 'meta_tag_keywords',
            'type'  => 'text',
            'limit' => MysqlAdapter::TEXT_TINY,
            'after' => 'meta_tag_desc'
        ],
        'pages' => [
            'column' => 'meta_tag_keywords',
            'type'  => 'text',
            'limit' => MysqlAdapter::TEXT_TINY,
            'after' => 'meta_tag_desc'
        ],
        'blog_categories' => [
            'column' => 'meta_tag_keywords',
            'type'  => 'string',
            'limit' => 250,
            'after' => 'page_title'
        ],
        'blog_entries' => [
            'column' => 'meta_tag_keywords',
            'type'  => 'string',
            'limit' => 250,
            'after' => 'tags'
        ],
        'blog_settings' => [
            'column' => 'meta_tag_keywords',
            'type'  => 'string',
            'limit' => 250,
            'after' => 'page_title'
        ],
        'developments' => [
            'column' => 'meta_keywords',
            'type'  => 'text',
            'limit' => MysqlAdapter::TEXT_TINY,
            'after'  => 'page_title'
        ]
    ];

    /**
     * Remove Meta Keyword Columns
     * @return void
     */
    public function up()
    {
        foreach ($this->tables as $table => $options) {
            $this->table($table)
                ->removeColumn($options['column'])
                ->save();
        }
    }

    /**
     * Readd Meta Keyword Columns
     * @return void
     */
    public function down()
    {
        foreach ($this->tables as $table => $options) {
            $this->table($table)
                ->addColumn(
                    $options['column'],
                    $options['type'],
                    [
                        'limit' => $options['limit'],
                        'after' => $options['after']
                    ]
                )
                ->save();
        }
    }
}
