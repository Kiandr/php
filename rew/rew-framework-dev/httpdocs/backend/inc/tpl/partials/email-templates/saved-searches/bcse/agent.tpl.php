<?php

/**
 * Saved Searches Email Template - Agent partial
 * @var array $style
 * @var array $agent
 */
?>

<!-- Wrap -->
<table class="wrap" border="0" cellpadding="0" cellspacing="0" width="600">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="290" align="left" class="wrap">
                <tr>
                    <td class="plr1">
                        <img
                            width="290"
                            border="0"
                            class="img"
                            src="<?= $site['url'] . (!empty($agent['image']) ? "/uploads/agents/" . $agent['image'] : "/img/email_templates/clear.gif"); ?>"
                            alt="Photo of <?=htmlspecialchars($agent['first_name']); ?> <?=htmlspecialchars($agent['last_name']); ?>"
                            style="display: block; padding: 0; color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: bold; font-size: 24px; max-width:100%; -webkit-border-radius: 4px; border-radius: 4px;" />
                    </td>
                </tr>
            </table>

            <table border="0" cellpadding="0" cellspacing="0" width="290" align="right" class="wrap">
                <tr>
                    <td class="plr1">
                        <h2 style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; font-size: 22px; line-height: 28px; padding: 20px 0 10px 0; margin: 0 0 0 0;">
                            <?=htmlspecialchars($agent['first_name']); ?> <?=htmlspecialchars($agent['last_name']); ?>
                        </h2>

                        <h3 style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-weight: normal; font-size: 18px; line-height: 22px; padding: 0 0 20px 0; margin: 0 0 0 0;">
                            <?=htmlspecialchars($agent['title']); ?>
                        </h3>

                        <div style="color: <?= $style["body_text"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 16px; line-height: 22px; padding: 0 0 10px 0;">
                            <?=htmlspecialchars((strlen($agent['remarks']) > 140 ? substr($agent['remarks'], 0,140) . "..." : $agent['remarks'])); ?> <br>
                            <?php if (!empty($agent['link'])) { ?>
                                <a href="<?=$agent['link']; ?>"
                                    target="_blank" title="View full agent biography"
                                    class="link"
                                    style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; color: <?= $style["body_text"]; ?>; text-decoration: underline;">Full Bio</a>
                            <?php } ?>
                        </div>

                        <?php if (!empty($agent['email'])) { ?>
                            <div style="font-family: <?= $style["font_stack"]; ?>; padding: 0 0 8px 0; color: <?= $style["body_text"]; ?>;">Email:
                                <a class="link" href="mailto:<?=$agent['email']; ?>" style="color: <?= $style["body_text"]; ?>; text-decoration: none;">
                                    <?=htmlspecialchars((strlen($agent['email']) > 37 ? substr($agent['email'], 0,34) . "..." : $agent['email'])); ?>
                                </a>
                            </div>
                        <?php } ?>

                        <?php if (!empty($agent['office_phone'])) { ?>
                            <div style="font-family: <?= $style["font_stack"]; ?>; color: <?= $style["body_text"]; ?>;">Phone:
                                <a class="link" href="tel:<?=$agent['office_phone']; ?>" style="color: <?= $style["body_text"]; ?>; text-decoration: none;">
                                    <?=htmlspecialchars($agent['office_phone']); ?>
                                </a>
                            </div>
                        <?php } ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
