<?php

/**
 * Saved Searches Email Template - Social Media Partial
 * @var array $style
 * @var array $links
 */

foreach ($links as $link) {
    if (!empty($link) && in_array($link["slug"], $settings["social_media"])) { ?>

        <table border="0" cellpadding="0" cellspacing="0" width="40" align="left"
               style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 40px !important;">
            <tbody>
            <tr>
                <td width="40"
                    style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 8px; width: 40px !important;">
                    <a
                            href="<?= $link["url"]; ?>" target="_blank"
                            title="Opens new browser window"
                            class="footer-social-icon"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block !important; margin-right: 8px !important; text-decoration: none;"><img
                                src="<?= Settings::getInstance()->SETTINGS['URL'] . "img/email_templates/icon-{$link["slug"]}.png"; ?>"
                                alt="<?= $link["name"]; ?>" border="0"
                                style="-ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none;"></a>
                </td>
            </tr>
            </tbody>
        </table>

<?php }
}