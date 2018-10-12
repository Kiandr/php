<?php

/**
 * Saved Searches Email Template - Intro Partial
 * @var array $style
 * @var array $user
 * @var array $search
 * @var array $site
 */

if (!empty($tags)) {
    $tags['site_link'] = "<a href=\"" . Format::htmlspecialchars($site["url"]) . "\" target=\"_blank\" title=\"Opens new browser window\" style=\"-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #ffffff; text-decoration: none;\">" . Format::htmlspecialchars($site["name"]) . "</a>";

    foreach ($tags as $tag => $value) {
        if ($tag != 'site_link') {
            $value = empty($value) ?: Format::htmlspecialchars($value);
        }
        $message = str_replace('{' . $tag . '}', $value, $message);
    }
}
?>

<tr>
    <td class="p2" style="padding: 20px 20px 20px 20px; color: #ffffff; font-family: <?= $style["font_stack"]; ?>; font-weight: bold; font-size: 32px; line-height: 36px;">
        <h1 style="margin: 0 0 0 0; color: #ffffff; font-family: <?= $style["font_stack"]; ?>; font-size: 24px; font-weight: normal; line-height: 36px;">
            <?= $message; ?>
        </h1>
    </td>
</tr>