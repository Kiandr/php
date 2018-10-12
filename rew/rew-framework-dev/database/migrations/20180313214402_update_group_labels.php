<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class UpdateGroupLabels extends AbstractMigration
{
    private $groupLabels = [
        'a' => 'red',
        'b' => 'rose',
        'c' => 'violet',
        'd' => 'purple',
        'e' => 'blue',
        'f' => 'azure',
        'g' => 'seafoam',
        'h' => 'lime',
        'i' => 'green',
        'j' => 'orange',
        'k' => 'blaze',
        'l' => 'grenadine',
        'm' => 'bean',
        'n' => 'almond',
        'o' => 'marigold',
        'p' => 'canary',
        'q' => 'yellow',
        'r' => 'grey'
    ];

    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_GROUPS'];
        $table = $this->table($tableName);

        $table->changeColumn('style', 'char', ['limit' => 255])
            ->save();

        foreach ($this->groupLabels as $key => $groupLabel) {
            $query = sprintf(
                "UPDATE `%s` SET `style` = '" . $groupLabel . "' WHERE `style` = '" . $key . "' ;",
                $tableName
            );
            $this->execute($query);
        }
    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $settings = $this->getContainer()->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_GROUPS'];
        $table = $this->table($tableName);

        foreach ($this->groupLabels as $key => $groupLabel) {
            $query = sprintf(
                "UPDATE `%s` SET `style` = '" . $key . "' WHERE `style` = '" . $groupLabel . "' ;",
                $tableName
            );
            $this->execute($query);
        }

        $table->changeColumn('style', 'char', ['limit' => 1])
            ->save();
    }

}