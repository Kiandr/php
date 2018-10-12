<?php

use REW\Phinx\Migration\AbstractMigration;

/**
 * More information on writing migrations is available here:
 * http://docs.phinx.org/en/latest/migrations.html
 */
class HoneypotUpdate extends AbstractMigration
{
    /**
     * Migrate Up
     * @return void
     */
    public function up()
    {
        $settings = Container::getInstance()->get(\REW\Core\Interfaces\SettingsInterface::class);

        $to ='<input type="text" name="registration_type" style="display: none;" tabindex="-1" autocomplete="off">';
        $remove = [
            '<input class="hidden" name="first_name" value="" autocomplete="off">',
            '<input class="hidden" name="last_name" value="" autocomplete="off">',
            '<input class="uk-hidden" name="first_name" value="" autocomplete="off">',
            '<input class="uk-hidden" name="last_name" value="" autocomplete="off">'
        ];
        
        $this->execute(
            sprintf(
                "UPDATE `%s` SET `code` = REPLACE(`code`, '%s', '%s') WHERE `code` LIKE '%s';",
                $settings['TABLES']['SNIPPETS'],
                '<input class="hidden" name="email" value="" autocomplete="off">',
                $to,
                '%<input class="hidden" name="email" value="" autocomplete="off">%'
            )
        );

        // Elite
        $this->execute(
            sprintf(
                "UPDATE `%s` SET `code` = REPLACE(`code`, '%s', '%s') WHERE `code` LIKE '%s';",
                $settings['TABLES']['SNIPPETS'],
                '<input class="uk-hidden" name="email" value="" autocomplete="off">',
                $to,
                '%<input class="uk-hidden" name="email" value="" autocomplete="off">%'
            )
        );

        // Clean up the rest
        foreach ($remove as $elem) {
            $this->execute(
                sprintf(
                    "UPDATE `%s` SET `code` = REPLACE(`code`, '%s', '') WHERE `code` LIKE '%s';",
                    $settings['TABLES']['SNIPPETS'],
                    $elem,
                    '%' . $elem . '%'
                )
            );
        }

    }

    /**
     * Migrate Down
     * @return void
     */
    public function down()
    {
        $settings = Container::getInstance()->get(\REW\Core\Interfaces\SettingsInterface::class);

        $from = '<input type="text" name="registration_type" style="display: none;" tabindex="-1" autocomplete="off">';
        $toMarkup = '<input class="hidden" name="email" value="" autocomplete="off">
            <input class="hidden" name="first_name" value="" autocomplete="off">
            <input class="hidden" name="last_name" value="" autocomplete="off">';

        $this->execute(
            sprintf(
                "UPDATE `%s` SET `code` = REPLACE(`code`, '%s', '%s') WHERE `code` LIKE '%s';",
                $settings['TABLES']['SNIPPETS'],
                $from,
                $toMarkup,
                '%' . $from . '%'
            )
        );

    }

}
