<?php

/**
 * Saved Searches Email Template - Agent partial
 * @var array $style
 * @var array $agent
 */
?>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="columns-table"
       style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; background-color: <?= $style["body_bg"]; ?>; border-bottom: 1px solid <?= $style["template_border"]; ?>; border-collapse: collapse !important; border-top: none; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
    <tbody>
    <tr>
        <td align="center" valign="top"
            style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
            <table border="0" cellpadding="0" cellspacing="0" align="left" width="290"
                   class="table-column"
                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <tbody>
                <tr>
                    <td class="table-column-td-agent"
                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-left: 0px;">
                        <img
                            src="<?= $site['url'] . (!empty($agent['image']) ? "/uploads/agents/" . $agent['image'] : "/img/email_templates/clear.gif"); ?>"
                            border="0" alt="Photo of <?=htmlspecialchars($agent['first_name']); ?> <?=htmlspecialchars($agent['last_name']); ?>" width="290"
                            class="agent-img"
                            style="-ms-interpolation-mode: bicubic; border: 0; height: auto; line-height: 100%; width: 290px; max-width: 290px; outline: none; text-decoration: none;">
                    </td>
                </tr>
                </tbody>
            </table><!-- /.table-column -->
            <table border="0" cellpadding="0" cellspacing="0" align="right" width="290"
                   class="table-column"
                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; border-collapse: collapse !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                <tbody>
                <tr>
                    <td class="table-column-td"
                        style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 20px; padding-right: 20px; padding-top: 0px; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 150%;">
                        <h2 class="agent agent-name"
                            style="color: <?= $style["body_text"]; ?> !important; display: block; font-family: <?= $style["font_stack"]; ?>; font-size: 22px; font-style: normal; font-weight: normal; letter-spacing: normal; line-height: 140%; margin-bottom: 0px; margin-right: 20px; margin-top: 20px; text-align: left;">
                            <?=htmlspecialchars($agent['first_name']); ?> <?=htmlspecialchars($agent['last_name']); ?></h2>
                        <div class="agent agent-role"
                             style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 18px; font-style: italic; line-height: 140%; margin-bottom: 20px; margin-right: 20px;">
                            <?=htmlspecialchars($agent['title']); ?>
                        </div>
                        <div class="agent agent-info"
                             style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 140%; margin-bottom: 20px; margin-right: 20px;">
                            <?=htmlspecialchars((strlen($agent['remarks']) > 140 ? substr($agent['remarks'], 0,140) . "..." : $agent['remarks'])); ?>
                            <br>
                            <?php if (!empty($agent['link'])) { ?>
                            <a href="<?=$agent['link']; ?>"
                                   target="_blank" title="Opens new browser window"
                                   class="agent-info-lnk"
                                   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_link"]; ?>; text-decoration: underline;">Full
                                Bio</a>
                            <?php } ?></div>
                        <?php if (!empty($agent['email'])) { ?>
                        <div class="agent agent-email"
                             style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 140%; margin-right: 20px;">
                            Email: <br><a
                                href="mailto:<?=$agent['email']; ?>"
                                class="agent-link"
                                style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_link"]; ?>; text-decoration: none;"><?=htmlspecialchars((strlen($agent['email']) > 37 ? substr($agent['email'], 0,34) . "..." : $agent['email'])); ?></a>
                        </div>
                        <?php } ?>
                        <?php if (!empty($agent['office_phone'])) { ?>
                        <div class="agent agent-phone"
                             style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 14px; line-height: 140%; margin-bottom: 20px;  margin-right: 20px;">
                            Office Phone: <a
                                href="tel:<?=$agent['office_phone']; ?>" class="agent-link"
                                style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_link"]; ?>; text-decoration: none;"><?=htmlspecialchars($agent['office_phone']); ?></a>
                        </div>
                        <?php } ?>
                    </td>
                </tr>
                </tbody>
            </table><!-- /.table-column --></td>
    </tr>
    </tbody>
</table><!-- /.columns-table -->