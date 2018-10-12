<?php

use REW\Phinx\Migration\AbstractMigration;

class DontShareEliteFooterQuoteSnippet extends AbstractMigration
{

    private $bootstrap = __DIR__ . '/../../boot/app.php';

    /**
     * Up Method.
     */
    public function up()
    {

        require_once $this->bootstrap;

        if (strtolower(Settings::getInstance()->SKIN) === 'elite') {
            $count = $this->execute(
                "UPDATE `snippets`
                    SET `agent` = 1
                    WHERE `name` = 'footer-quote'
                    AND `agent` IS NULL
                    AND `team` IS NULL;"
            );
        }
    }

    /**
     * Down Method.
     */
    public function down()
    {
        require_once $this->bootstrap;

        if (strtolower(Settings::getInstance()->SKIN) === 'elite') {
            $count = $this->execute(
                "UPDATE `snippets`
                SET `agent` = NULL
                WHERE `name` = 'footer-quote'
                AND `agent` = 1;"
            );
        }
    }
}
