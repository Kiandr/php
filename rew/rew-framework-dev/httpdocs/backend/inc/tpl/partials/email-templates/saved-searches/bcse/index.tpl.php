<?php

/**
 * Saved Searches email template
 * @var array $search
 * @var string $permalink
 * @var array $site
 * @var array $user
 * @var array $listings
 * @var array $agent
 * @var array $social_media
 * @var array $office
 * @var string $unsubscribe
 * @var string $sub_preferences
 */

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="format-detection" content="telephone=no">
        <meta name="format-detection" content="address=no">
        <meta name="format-detection" content="email=no">
        <title>Saved Searches</title>
        <?= $this->render(__DIR__ . '/head.css.php', ['style' => $style]); ?>
    </head>

    <body style="margin: 0; padding: 0;">

        <!-- Contain -->
        <table border="0" cellpadding="0" width="100%">
            <tr>
                <td align="center" style="padding: 6px 6px 6px 6px;">
                    <!-- Wrap -->
                    <table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600">
                        <tr>
                            <td align="right" id="web_view">
                                Email not displaying correctly?<br>
                                <a href="<?= $permalink; ?>" target="_blank" title="Opens new browser window" style="color: <?= $style["body_text"]; ?>; text-decoration: none; font-size: 14px; font-family: <?= $style["font_stack"]; ?>;">
                                    View it in your browser
                                </a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align="center" style="padding: 20px 0 20px 0; background-color: #ffffff;" bgcolor="#ffffff">

                    <!-- Hidden preheader -->
                    <div style="display: none; font-size: 1px; color: #ffffff; line-height: 1px; font-family: <?= $style["font_stack"]; ?>; max-height: 0px; max-width: 0px; opacity: 0; overflow: hidden; mso-hide: all;">
                        <?= $search["count"]; ?> new properties from your search for <?= $search["title"]; ?>
                    </div>

                    <!-- Wrap -->
                    <table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600">
                        <tr>
                            <td>

                                <!-- Logo & Preheader -->
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <?php if (!empty($params["logo"]['file'])) { ?>
                                            <td align="left" class="logo">
                                                <a href="<?= $site["url"]; ?>" title="<?= $site["name"]; ?>" target="_blank" style="color: <?= $style["body_text"]; ?>; text-decoration: none;">
                                                    <img src="<?= $site["url"] . "/uploads/" . $params["logo"]['file']; ?>" alt="<?= $site["name"]; ?>" width="300" border="0" style="display: block; padding: 0; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: bold; font-size: 24px; max-width:100%;" />
                                                </a>
                                            </td>
                                        <?php } ?>
                                        <td align="right" class="hidden" style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; font-size: 14px; line-height: 18px;">
                                            <?= $search["count"]; ?> new properties from your search for <?= $search["title"]; ?>
                                        </td>
                                    </tr>
                                </table>

                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <!-- Contain -->
        <table border="0" cellpading="0" cellspacing="0" width="100%">
            <tr>
                <td align="center" style="padding: 20px 0 20px 0; background-color: <?= $style["body_bg"]; ?>;" bgcolor="<?= $style["body_bg"]; ?>">

                    <!-- Wrap -->
                    <table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600">
                        <tr>
                            <td>
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">

                                    <!-- START INTRODUCTION -->
                                    <?php if ($params['message']['display'] == 'true') { ?>
                                        <?= $this->render(__DIR__ . '/intro.tpl.php', [
                                                'style' => $style,
                                                'user' => $user,
                                                'search' => $search,
                                                'site' => $site,
                                                'message' => $message,
                                                'tags' => [
                                                    'first_name'	=> $user['first_name'],
                                                    'last_name'		=> $user['last_name'],
                                                    'email'		    => $user['email'],
                                                    'result_count'  => $search['count'],
                                                    'search_title'  => $search['title']
                                                ]
                                            ]);
                                        ?>
                                    <?php } ?>
                                    <!-- END INTRODUCTION -->

                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <!-- START LISTINGS -->

        <!-- LISTING COLUMNS -->

        <?php if (!empty($listings)) { ?>
            <?= $this->render(__DIR__ . '/results.tpl.php', [
                    'style' => $style,
                    "listings" => $listings]); ?>
        <?php } ?>


        <!-- Contain -->
        <!-- View All Listings -->
        <table border="0" cellpading="0" cellspacing="0" width="100%" style="table-layout: fixed;">
            <tr>
                <td align="center" style="padding: 20px 0 20px 0;">

                    <!-- Wrap -->
                    <table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600">
                        <tr>
                            <td style="padding: 20px 20px 20px 20px; border-top: 2px solid #cccccc; border-bottom: 2px solid #cccccc;">
                                <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td class="plr1" style="font-size: 18px;">
                                            <span style="font-size: 18px; color: <?= $style["body_text"]; ?>;">
                                                Showing <b><?= (count($listings) + (empty($hero) ? 0 : 1)); ?> of <?= $search["count"]; ?> new properties</b> that match your saved search for <i><?= $search["title"]; ?></i>
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td align="center" style="padding: 10px 0 10px 0;">
                                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                <tr>
                                                    <td align="center" style="padding: 20px 0 0 0;" class="plr1">
                                                        <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                            <tr>
                                                                <td align="center" bgcolor="<?= $style["button_bg"]; ?>" class="button" style="-webkit-border-radius: 3px; border-radius: 3px; background-color: <?= $style["button_bg"]; ?>;">
                                                                    <a href="<?= $search["url"]; ?>" target="_blank" title="View all properties" style="padding: 12px 18px 12px 18px; font-size: 16px; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; color: #ffffff; text-decoration: none; display: block;">View All Properties</a>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>

                </td>
            </tr>
        </table>

        <!-- END LISTINGS -->

        <!-- Contain -->
        <!-- START LISTING AGENT -->
        <table border="0" cellpading="0" cellspacing="0" width="100%">
            <tr>
                <td align="center" style="padding: 20px 0 20px 0;">

                    <?php if ($params['agent']['display'] == 'true') { ?>
                        <?= $this->render(__DIR__ . '/agent.tpl.php', [
                            'style' => $style,
                            'agent' => $agent,
                            'site' => $site
                        ]);
                        ?>
                    <?php } ?>

                </td>
            </tr>
        </table>
        <!-- END LISTING AGENT -->

        <!-- Foot -->
        <table border="0" cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed;">
            <tr>
                <td align="center" style="padding: 20px 0 20px 0; background-color: <?= $style["body_bg"]; ?>;" bgcolor="<?= $style["body_bg"]; ?>">

                    <!-- Wrap -->
                    <!-- Social Icons -->
                    <table border="0" cellpadding="0" cellspacing="0" align="center" style="table-layout: fixed; text-align: center;">
                        <tr>
                            <?= $this->render(__DIR__ . '/social_media.tpl.php', [
                                'settings' => $settings,
                                'style' => $style,
                                'links' => $social_media
                            ]);
                            ?>
                        </tr>
                    </table>

                    <!-- Wrap -->
                    <table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600" align="center">
                        <tr>
                            <td class="text-padding" align="center" style="padding: 10px 0 10px 0; color: #ffffff; font-family: Arial, sans-serif; font-weight: normal; font-size: 14px; line-height: 18px;">
                                <a class="link" href="<?= $unsubscribe; ?>" title="Unsubscribe from this list" style="color: #ffffff; text-decoration: none;">Unsubscribe from this list.</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-padding" align="center" style="padding: 10px 0 10px 0; color: #ffffff; font-family: Arial, sans-serif; font-weight: normal; font-size: 14px; line-height: 18px;">
                                <a class="link" href="<?= $sub_preferences; ?>" title="Update subscription preferences" style="color: #ffffff; text-decoration: none;">Update subscription preferences</a>
                            </td>
                        </tr>

                        <? if (!empty($office)) { ?>
                            <tr>
                                <td class="text-padding" align="center" style="padding: 10px 0 10px 0; color: #ffffff; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; font-size: 14px; line-height: 18px;">
                                    <span class="link" style="color: #ffffff; text-decoration: none;">
                                        Our mailing address is:<br>
                                        <?= $office["title"]; ?><br>
                                        <?= $office["address"]; ?>,
                                        <?= $office["city"]; ?>,
                                        <?= $office["state"]; ?>,<br>
                                        <?= $office["country"]; ?>
                                        <?= $office["zip"]; ?>
                                    </span><br>
                                    <span><em>&copy;Copyright <?= date('Y'); ?> <?= !empty($office["name"]) ? $office["name"] : $site["name"]; ?>, All rights reserved.</em></span>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>

                </td>
            </tr>
        </table>

    </body>
</html>