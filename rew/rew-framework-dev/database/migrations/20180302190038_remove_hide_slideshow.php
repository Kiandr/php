<?php

use REW\Phinx\Migration\AbstractMigration;

class RemoveHideSlideshow extends AbstractMigration
{
    private $tables = [
        'blog_settings' => [
            'column' => 'hide_slideshow',
            'type'  => 'enum',
            'values' => ['t','f'],
            'default' => 'f',
            'after' => 'meta_tag_desc'
        ],
        'pages' => [
            'column' => 'hide_slideshow',
            'type'  => 'enum',
            'values' => ['t','f'],
            'default' => 'f',
            'after' => 'hide_sitemap'
        ]
    ];

    /**
     * Remove Hide Slideshow Columns
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
     * Add Hide Slideshow Columns
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
                        'values' => $options['values'],
                        'default' => $options['default'],
                        'after' => $options['after']
                    ]
                )
                ->save();
        }
    }
}
