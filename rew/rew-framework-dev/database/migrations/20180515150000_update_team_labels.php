<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class UpdateTeamLabels extends AbstractMigration
{

    private $teamLabels = [
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
        $di = Container::getInstance();
        $settings = $di->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_TEAMS'];
        $table = $this->table($tableName);

        $table->changeColumn('style', 'char', ['limit' => 32])->save();

        foreach ($this->teamLabels as $key => $label) {
            $this->execute(sprintf("UPDATE `%s` SET `style` = '%s' WHERE `style` = '%s';", $tableName, $label, $key));
        }

    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $di = Container::getInstance();
        $settings = $di->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_TEAMS'];
        $table = $this->table($tableName);

        foreach ($this->teamLabels as $key => $label) {
            $this->execute(sprintf("UPDATE `%s` SET `style` = '%s' WHERE `style` = '%s';", $tableName, $key, $label));
        }

        $table->changeColumn('style', 'char', ['limit' => 1])->save();

    }

}
