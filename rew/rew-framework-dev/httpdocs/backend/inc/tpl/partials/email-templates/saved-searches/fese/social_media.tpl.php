<?php

/**
 * Saved Searches Email Template - Social Media Partial
 * @var array $style
 * @var array $links
 */

foreach ($links as $link) {
    if (!empty($link) && in_array($link["slug"], $settings["social_media"])) { ?>

        <td width="45" style="width: 45px;">
            <table border="0" cellpadding="0" cellspacing="0" width="40" style="display: inline-block;">
                <tr>
                    <td style="width: 40px;">
                        <a href="<?= $link["url"]; ?>" target="_blank" title="Opens new browser window" style="color: #ffffff; text-decoration: none;">
                            <img src="<?= Settings::getInstance()->SETTINGS['URL'] . "img/email_templates/icon-{$link["slug"]}.png"; ?>" alt="<?= $link["name"]; ?>" style="color: #ffffff; font-size: 12px;" />
                        </a>
                    </td>
                </tr>
            </table>
        </td>

    <?php }
}