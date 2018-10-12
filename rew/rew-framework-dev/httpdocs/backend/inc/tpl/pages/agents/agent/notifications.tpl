<?php
// Render agent summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
    'title' => __('Notifications'),
    'agent' => $agent,
    'agentAuth' => $agentAuth
]);
?>
<form action="?submit" method="post" class="rew_check">

    <input type="hidden" name="id" value="<?=$agent['id']; ?>">

    <div class="btns btns--stickyB">
        <span class="R">
            <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
        </span>
    </div>

<div class="block">

    <div class="field">
        <label class="field__label"><?= __('Send notifications to:'); ?></label>
        <input type="email" class="w1/1" value="<?=htmlspecialchars($agent['email']); ?>" readonly>
    </div>

    <?php

    // Display Incoming Notification Settings
    foreach ($notifications->getIncoming() as $k => $notify) {

        // Toggle Shark Tank Notifications Settings
        if ($k == Backend_Agent_Notifications::INCOMING_SHARK_TANK_LEADS) {
            if (
                empty(Settings::getInstance()->MODULES['REW_SHARK_TANK'])
                || empty($leadsAuth->canAccessSharkTank($authuser))
                || !$isSharktankEnabled
            ) {
                continue;
            }
        }

        echo '<div class="field">';
        echo '<label class="field__label">';
        echo $notify['title'];
        echo '</label>';

        if (!empty($notify['tip'])) echo '<p class="text--mute">' . $notify['tip'] . '</p>';
        echo '<div class="-marB8">';
        // Email Settings
        if (!empty($notify['email'])) {
            echo '<label class="toggle"><input type="checkbox" name="settings[incoming][' . $k . '][email]" value="1"' . (!empty($settings['incoming'][$k]['email']) ? ' checked' : '') . '> <span class="toggle__label">' . __('Email') . '</span></label>';
        // Settings Locked
        } else {
            echo '<label class="toggle"><input type="checkbox" disabled checked> <span class="toggle__label">' . __('Email') . '</span></label>';
            echo '<input type="hidden" name="settings[incoming][' . $k . '][email]" value="1">' . PHP_EOL;
        }

        // SMS Settings
        if (!empty($notify['sms'])) {
            if (!empty($agent['sms_email'])) {
                echo '<label class="toggle"><input type="checkbox" name="settings[incoming][' . $k . '][sms]" value="1"' . (!empty($settings['incoming'][$k]['sms']) ? ' checked' : '') . '> <span class="toggle__label">' . __('SMS') . '</span></label>';
            // Disabled
            } else {
                echo '<label class="toggle"><input type="checkbox" disabled> <span class="toggle__label">' . __('SMS') . '</span></label>' . PHP_EOL;
            }
        }
        echo '</div>';

        // CC Settings
        $cc = $settings['incoming'][$k]['cc'];
        if (!empty($notify['cc'])) {
            echo '<div>';
            echo '<a href="#cc" class="add-cc' . (empty($cc) ? '' : ' hidden') . '">' . __('Add CC') . '</a>';
            echo '<div class="w1/1 input' . (empty($cc) ? ' hidden' : '') . '">';
            echo '<input class="w1/1" type="email" name="settings[incoming][' . $k . '][cc]" value="' . htmlspecialchars($cc) . '" placeholder="' . __('CC Email Address') . '">';
            echo '</div>';
            echo '</div>';
        }

        echo '</div>';

    }

    ?>
</div>
<div class="block" style="border: 1px solid #ddd; margin: 16px;">
    <div class="field">
        <label class="field__label"><?= __('The following emails sent to assigned leads should include:'); ?></label>
        <input class="w1/1" type="email" name="settings[email]" placeholder="<?= __('Notification Email Address'); ?>" value="<?=htmlspecialchars($settings['email']); ?>">
    </div>
    <?php
    // Display Outgoing Notification Settings
    foreach ($notifications->getOutgoing() as $k => $notify) {
        echo '<div>';
        echo '<h3 class="divider">';
        if (!empty($notify['tip'])) {
            echo ' ';
        }
        echo '<div class="divider__label divider__label--left">' . $notify['title'] . '</div>';
        echo '</h3>';
        if (!empty($notify['tip'])) {
            echo '<p class="text--mute">' . $notify['tip'] . '</p>';
        }
        echo '</div>';
        echo '<div class="toggle">';
        echo '<input id="no_' . $k . '" type="radio" name="settings[outgoing][' . $k . ']" value=""' . (empty($settings['outgoing'][$k]) ? ' checked' : '') . '>';
        echo '<label class="toggle__label" for="no_' . $k . '">' . __('Off') . '</label> ';
        echo '<input id="cc_' . $k . '" type="radio" name="settings[outgoing][' . $k . ']" value="cc"' . ($settings['outgoing'][$k] == 'cc' ? ' checked' : '') . '>';
        echo '<label class="toggle__label" for="cc_' . $k . '"> ' . __('CC') . '</label> ';
        echo '<input id="bcc_' . $k . '" type="radio" name="settings[outgoing][' . $k . ']" value="bcc"' . ($settings['outgoing'][$k] == 'bcc' ? ' checked' : '') . '>';
        echo '<label class="toggle__label" for="bcc_' . $k . '">' . __('BCC') . '</label> ';
        echo '</div>';
    }
    ?>
</div>

</form>
