<?php

/**
 * Saved Searches Email Template - Intro Partial
 * @var array $style
 * @var array $user
 * @var array $search
 * @var array $site
 */

if (!empty($tags)) {
    $tags['site_link'] = "<a href=\"" . Format::htmlspecialchars($site["url"]) . "\" target=\"_blank\" title=\"Opens new browser window\" style=\"-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: " . $style["body_link"] . " text-decoration: none;\">" . Format::htmlspecialchars($site["name"]) . "</a>";

    foreach ($tags as $tag => $value) {
        if ($tag != 'site_link') {
            $value = empty($value) ?: Format::htmlspecialchars($value);
        }
        $message = str_replace('{' . $tag . '}', $value, $message);
    }
}
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%"
       class="body-table introduction-table"
       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["body_bg"]; ?>; border-collapse: collapse !important; ; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
    <tr>
        <td valign="top" class="body-td"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 16px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 20px; text-align: left;">
            <h1 style="color: <?= $style["body_text"]; ?> !important; display: block; font-family: <?= $style["font_stack"]; ?>; font-size: 24px; font-style: normal; font-weight: normal; letter-spacing: normal; line-height: 140%; margin-bottom: 20px; margin-left: 0; margin-right: 0; margin-top: 0; text-align: left;">
                <?= $message; ?>
            </h1></td>
    </tr>
    </tbody>
</table><!-- /.body-table .introduction-table -->