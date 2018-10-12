<?php

use REW\Phinx\Migration\AbstractMigration;

class ShareVisionSiteSignupSnippet extends AbstractMigration
{

    private $bootstrap = __DIR__ . '/../../boot/app.php';

    public function up () {
        require_once $this->bootstrap;

        if (strtolower(Settings::getInstance()->SKIN) === strtolower('REW\Theme\Enterprise\Theme')) {
            $count = $this->execute(
                "UPDATE `snippets`
                    SET `agent` = NULL
                    WHERE `name` = 'site-signup-cta'
                    AND `agent` = 1;"
            );
        }
    }

    public function down () {
        require_once $this->bootstrap;

        if (strtolower(Settings::getInstance()->SKIN) === strtolower('REW\Theme\Enterprise\Theme')) {
            $count = $this->execute(
                "UPDATE `snippets`
                    SET `agent` = 1
                    WHERE `name` = 'site-signup-cta'
                    AND `agent` IS NULL;"
            );
        }
    }
}
