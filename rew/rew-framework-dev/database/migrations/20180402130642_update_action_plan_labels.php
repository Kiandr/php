<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class UpdateActionPlanLabels extends AbstractMigration
{
    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $di = Container::getInstance();
        $labelColours = $di->get(REW\Backend\Store\LabelColourStore::class);
        $actionPlanLabels = $labelColours->getLabelColours();

        $settings = $di->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_ACTION_PLANS'];
        $table = $this->table($tableName);

        $table->changeColumn('style', 'char', ['limit' => 32])->save();

        foreach ($actionPlanLabels as $key => $label) {
            $this->execute(sprintf("UPDATE `%s` SET `style` = '%s' WHERE `style` = '%s';", $tableName, $key, $label));
        }

    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $di = Container::getInstance();
        $labelColours = $di->get(REW\Backend\Store\LabelColourStore::class);
        $actionPlanLabels = $labelColours->getLabelColours();

        $settings = $di->get(\REW\Core\Interfaces\SettingsInterface::class);
        $tableName = $settings['TABLES']['LM_ACTION_PLANS'];
        $table = $this->table($tableName);

        foreach ($actionPlanLabels as $key => $label) {
            $this->execute(sprintf("UPDATE `%s` SET `style` = '%s' WHERE `style` = '%s';", $tableName, $key, $label));
        }

        $table->changeColumn('style', 'char', ['limit' => 1])->save();

    }

}
