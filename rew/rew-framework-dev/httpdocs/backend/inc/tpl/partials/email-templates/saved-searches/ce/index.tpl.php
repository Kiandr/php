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
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="address=no">
    <meta name="format-detection" content="email=no">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Saved Searches</title>
    <?= $this->render(__DIR__ . '/head.css.php', ['style' => $style]); ?>
</head>
<body text="<?= $style["body_text"]; ?>" link="<?= $style["body_link"]; ?>"
      alink="<?= $style["body_link"]; ?>" vlink="<?= $style["body_link"]; ?>" marginwidth="0" marginheight="0"
      topmargin="0" leftmargin="0" offset="0"
      style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["page_bg"]; ?>; height: 100% !important; margin: 0 !important; padding: 0; width: 100% !important;"
      bgcolor="<?= $style["page_bg"]; ?>">
<?= $this->render(__DIR__ . '/body.css.php', ['style' => $style]); ?>
<center>
    <!-- Outer Container -->
    <table border="0" cellpadding="0" cellspacing="0" width="100%" align="center" class="container-outer-table"
           style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["page_bg"]; ?>; border-collapse: collapse !important; height: 100% !important; margin: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 0; width: 100% !important;">
        <tbody>
        <tr>
            <td align="center" valign="top" class="container-outer-td"
                style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; height: 100% !important; margin: 0; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding: 20px; width: 100% !important;">
                <!-- Inner Container -->
                <table border="0" cellpadding="0" cellspacing="0" width="602" class="container-inner-table"
                       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border: 1px solid <?= $style["template_border"]; ?>; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;">
                    <tbody>
                    <tr>
                        <td align="center" valign="top"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <!-- START PRE-HEADER ***************************************************************************** -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="pre-header-table"
                                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-bottom: 1px solid <?= $style["template_border"]; ?>; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tbody>
                                <tr>
                                    <td class="pre-header-content pre-header-content-intro"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["preheader_bg"]; ?>; color: <?= $style["preheader_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 10px; line-height: 125%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 10px; padding-left: 20px; padding-right: 20px; padding-top: 10px; text-align: left;">
                                        <?= $search["count"]; ?> new properties from your search for <?= $search["title"]; ?>
                                    </td>
                                    <td valign="top" width="180"
                                        class="pre-header-content pre-header-content-browser"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["preheader_bg"]; ?>; color: <?= $style["preheader_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 10px; line-height: 125%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 10px; padding-left: 0px; padding-right: 20px; padding-top: 10px; text-align: center;" id="web_view">
                                        Email not displaying correctly?<br>
                                        <a href="<?= $permalink; ?>" target="_blank" title="Opens new browser window"
                                           class="pre-header-link"
                                           style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["preheader_link"]; ?>;">View
                                            it in your browser</a></td>
                                </tr>
                                </tbody>
                            </table><!-- /.pre-header-table -->
                            <!-- END PRE-HEADER ******************************************************************************* -->

                        </td>
                    </tr>
                    <!-- New row for Inner Container -->
                    <tr>
                        <td align="center" valign="top"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <!-- START HEADER ********************************************************************************* -->
                            <?php if (!empty($params["logo"]['file'])) { ?>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="header-table"
                                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["logo_bg"]; ?>; border-bottom: 1px solid <?= $style["template_border"]; ?>; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tbody>
                                <tr>
                                    <td valign="top" align="center" class="header-logo-td"
                                        bgcolor="<?= $style["logo_bg"]; ?>"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 20px; font-weight: bold; line-height: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 0; padding-left: 0; padding-right: 0; padding-top: 0; text-align: center; vertical-align: middle;">
                                        <a href="<?= $site["url"]; ?>" title="<?= $site["name"]; ?>"
                                           style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_link"]; ?>;"><img
                                                    src="<?= $site["url"] . "/uploads/" . $params["logo"]['file']; ?>"
                                                    class="header-logo-img" border="0" alt="<?= $site["name"]; ?>"
                                                    style="-ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; max-width: 590px; outline: none; text-decoration: none; width: auto  !important; padding-top: 16px; padding-bottom: 16px;"></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table><!-- /.header-table -->
                            <?php } ?>
                            <!-- END HEADER *********************************************************************************** -->

                        </td>
                    </tr>
                    <!-- New row for Inner Container -->
                    <tr>
                        <td align="center" valign="top"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <!-- START INTRODUCTION *************************************************************************** -->
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
                            <!-- END INTRODUCTION ***************************************************************************** -->

                        </td>
                    </tr>
                    <!-- New row for Inner Container -->
                    <tr>
                        <td align="center" valign="top"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <!-- START LISTINGS ******************************************************************************* -->
                            <!-- LISTING COLUMNS -->
                            <?php if (!empty($listings)) { ?>
                                <?= $this->render(__DIR__ . '/results.tpl.php', [
                                        'style' => $style,
                                        "listings" => $listings]); ?>
                            <?php } ?>
                            <!-- View All Listings -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                   class="columns-table hero-table"
                                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["body_bg"]; ?>; border-top: 1px solid <?= $style["template_border"]; ?>; border-bottom: 1px solid <?= $style["template_border"]; ?>; border-collapse: collapse !important; border-top: none; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tbody>
                                <tr>
                                    <td align="center" valign="top"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                        <table border="0" cellpadding="0" cellspacing="0" width="100%"
                                               class="table-column"
                                               style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                            <tbody>
                                            <tr>
                                                <td class="table-column-td view-all-td"
                                                    style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 20px; padding-left: 20px; padding-top: 20px; padding-right: 20px; font-family: <?= $style["font_stack"]; ?>; font-size: 16px; line-height: 140%; color: <?= $style["body_text"]; ?>;">
                                                    Showing <b><?= (count($listings) + (empty($hero) ? 0 : 1)); ?> of <?= $search["count"]; ?> new properties</b> that match your saved
                                                    search for <i><?= $search["title"]; ?></i>.<br><a
                                                            href="<?= $search["url"]; ?>"
                                                            target="_blank" title="Opens new browser window"
                                                            class="btn-more-details"
                                                            style="display: block; margin: 8px 0 8px 0; border-radius: <?= $style["button_radius"]; ?>; background-color: <?= $style["button_bg"]; ?>; font-size: 15px; line-height: 40px; text-align: center; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: #ffffff; text-decoration: none;"><b>View
                                                            all properties</b></a></td>
                                            </tr>
                                            </tbody>
                                        </table><!-- /.table-column --></td>
                                </tr>
                            </table>
                            <!-- END LISTINGS ********************************************************************************* -->

                        </td>
                    </tr>
                    <!-- New row for Inner Container -->
                    <tr>
                        <td align="center" valign="top"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <!-- START LISTING AGENT ************************************************************************** -->
                            <?php if ($params['agent']['display'] == 'true') { ?>
                            <?= $this->render(__DIR__ . '/agent.tpl.php', [
                                'style' => $style,
                                'agent' => $agent,
                                'site' => $site
                            ]);
                            ?>
                            <?php } ?>
                            <!-- END LISTING AGENT **************************************************************************** -->
                        </td>
                    </tr>
                    <!-- New row for Inner Container -->
                    <tr>
                        <td align="center" valign="top"
                            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">

                            <!-- START FOOTER ********************************************************************************* -->
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="footer-table"
                                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["footer_bg"]; ?>; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tbody>
                                <tr>
                                    <td valign="top" class="footer-td footer-td-icon"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["footer_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-left: 20px; padding-right: 20px; padding-top: 20px; text-align: left;">
                                        <?= $this->render(__DIR__ . '/social_media.tpl.php', [
                                            'settings' => $settings,
                                            'style' => $style,
                                            'links' => $social_media
                                        ]);
                                        ?>
                                    </td>
                                </tr>
                                <? if (!empty($office)) { ?>
                                <tr>
                                    <td valign="top" class="footer-td footer-td-address"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["footer_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-left: 20px; padding-right: 20px; padding-top: 0px; text-align: left;">
                                        <p style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; font-size: 14px; line-height: 20px;">
                                            <strong>Our mailing address is:</strong>
                                        </p>
                                        <div style="color: <?= $style["footer_text"]; ?>;text-decoration: none;">
                                            <?= $office["title"]; ?>
                                        </div>
                                        <div style="color: <?= $style["footer_text"]; ?> !important;text-decoration: none !important;width: 100%;">
                                            <font color="<?= $style["footer_text"]; ?>"
                                                  style="color:<?= $style["footer_text"]; ?> !important;text-decoration: none !important;"><?= $office["address"]; ?>,</font>
                                            <font color="<?= $style["footer_text"]; ?>"
                                                    style="color:<?= $style["footer_text"]; ?> !important;text-decoration: none !important;"><?= $office["city"]; ?>
                                                <?= $office["state"]; ?></font>
                                        </div>
                                        <div style="color: <?= $style["footer_text"]; ?> !important;text-decoration: none !important;width: 100%;">
                                            <font
                                                    color="<?= $style["footer_text"]; ?>"
                                                    style="color:<?= $style["footer_text"]; ?> !important;text-decoration: none !important;"><?= $office["country"]; ?></font>
                                            <font
                                                    color="<?= $style["footer_text"]; ?>"
                                                    style="color:<?= $style["footer_text"]; ?> !important;text-decoration: none; !important"><?= $office["zip"]; ?></font>
                                        </div>
                                    </td>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td valign="top" colspan="2" align="center"
                                        class="footer-td footer-td-copyright"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["footer_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-left: 20px; padding-right: 20px; padding-top: 0px; text-align: left;">
                                        <em>&copy;Copyright <?= date('Y'); ?> <?= !empty($office["name"]) ? $office["name"] : $site["name"]; ?>, All rights reserved.</em></td>
                                </tr>
                                <tr>
                                    <td valign="top" colspan="2" class="footer-td footer-td-subscription"
                                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["footer_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-left: 20px; padding-right: 20px; padding-top: 0px; text-align: left;">
                                        <a href="<?= $unsubscribe; ?>" title="Opens new browser window"
                                           style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block; color: <?= $style["footer_link"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-right: 12px; text-align: left;">Unsubscribe
                                            from this list.</a>

                                        <a href="<?= $sub_preferences; ?>" title="Opens new browser window"
                                           style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block; color: <?= $style["footer_link"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; text-align: left;">Update subscription preferences</a><br></td>
                                </tr>
                                </tbody>
                            </table><!-- /.footer-table -->
                            <!-- END FOOTER *********************************************************************************** -->

                        </td>
                    </tr>
                    </tbody>
                </table><!-- /.container-inner-table --></td><!-- /.container-outer-td -->
        </tr>
        </tbody>
    </table><!-- /.container-outer-table -->
</center>
<!-- This table is an Android specific hack -->
<table cellpadding="0" cellspacing="0" border="0" class="hide">
    <tr>
        <td height="1" class="hide" style="min-width:700px; font-size:0px;line-height:0px;">
            <img height="1" src="<?= $site["url"];?>/img/email_templates/clear.gif"
                 style="min-width: 700px; text-decoration: none; border: none; -ms-interpolation-mode: bicubic;"/>
        </td>
    </tr>
</table>
</body>
</html>
