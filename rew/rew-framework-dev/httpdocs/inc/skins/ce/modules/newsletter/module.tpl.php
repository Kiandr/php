<div class="divider -pad-vertical">
    <div class="divider__label -left">Details</div>
</div>
<?php if($user->info('newOptIn') === true) { ?>
    <p>You have subscribed to receiving newsletters.</p>
<?php } else { ?>
    <p>You have already subscribed to receiving newsletters.</p>
<?php } ?>
<a href="<?= $unsubscribe; ?>" title="Opens new browser window"
   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block; color: <?= $style["footer_link"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; padding-right: 12px; text-align: left;">Unsubscribe
    from this list.</a>

<a href="<?= $sub_preferences; ?>" title="Opens new browser window"
   style="-ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; display: inline-block; color: <?= $style["footer_link"]; ?>; font-family: <?= $style["font_stack"]; ?>; font-size: 11px; line-height: 150%; mso-table-lspace: 0pt; mso-table-rspace: 0pt; padding-bottom: 12px; text-align: left;">Update subscription preferences</a><br>
