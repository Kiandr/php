<?php

/**
 * @var bool $account
 * @var array $settings
 * @var array $agents
 */

?>
<form method="post" class="rew_check">


    <div class="bar">
        <div class="bar__title"><?= __('First Call Agent (FCA)'); ?></div>
        <div class="bar__actions">
            <?php if (isset($_GET['setup'])) { ?>
            <a class="bar__action" href="/backend/settings/partners/firstcallagent/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            <?php } else { ?>
            <a class="bar__action" href="/backend/settings/partners/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            <?php } ?>
        </div>
    </div>

    <div class="block">

        <div class="btns btns--stickyB"> <span class="R">
		<?php if ($account) { ?>
            <button class="btn btn--positive" type="submit" name="save"><svg class="icon icon-check mar0"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
            <button class="btn delete" type="submit" name="disable" onclick="javascript:return confirm('Are you sure you want to disable the integration with this partner?');"><?= __('Disable'); ?></button>
        <?php } ?>
	</span> </div>
        <?php if (!$account) { ?>
            <div>
                <img src="/backend/img/hlp/setup.png"/>
                <h1><?= __('Set Up First Call Agent Integration'); ?></h1>
                <p><?= __('First Call Agent integration is currently %s. To use this feature, you will need an integration key that FCA will provide you with.', '<strong>' . __('inactive') .'</strong>'); ?></p>
                <p><button class="btn btn--positive" type="submit" name="setup"><svg class="icon icon-add mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg> <?= __('Set Up First Call Agent Integration'); ?></button></p>
            </div>
        <?php } else { ?>

            <div>
                <div class="field">
                    <label class="field__label"><?= __('FCA Sending'); ?></label>
                    <div class="toggle">
                        <input type="radio" name="sending" id="sending_true" value="true"<?=($settings['sending'] == 'true') ? ' checked="checked"' : ''; ?>>
                        <label class="boolean toggle__label" for="sending_true"><?= __('On'); ?></label>
                        <input type="radio" name="sending" id="sending_false" value="false"<?=($settings['sending'] != 'true') ? ' checked="checked"' : ''; ?>>
                        <label class="boolean toggle__label" for="sending_false"><?= __('Off'); ?></label>
                    </div>
                    <p class="text--mute"><?= __('If this is on, new leads will be sent to the FCA system. Only leads that provided a phone number will be sent.'); ?></p>
                </div>

                <div class="field -marB">
                    <label class="field__label"><?= __('FCA API Key'); ?></label>
                    <input class="w1/1" type="text" name="api_key" value="<?=htmlentities($settings['api_key']); ?>">
                </div>
                <?php if (!empty($agents) && is_array($agents)) { ?>
                <div>
                    <label class="field__label"><?= __('FCA Excluded Agents'); ?></label>
                    <select multiple data-selectize class="w1/1" name="exclude_agents[]">
                        <?php foreach ($agents as $agent) { ?>
                        <option value="<?=$agent['id'];?>"<?=in_array($agent['id'], $settings['exclude_agents']) ? ' selected' : ''; ?>><?=htmlspecialchars($agent['name']); ?></option>
                        <?php } ?>
                    </select>
                    <p class="text--mute"><?= __('Leads assigned to the selected agents will NOT be sent to FCA system. {spanStart}If no agents are selected, all leads will be sent{spanEnd}.', ['{spanStart}' => '<span style="font-weight:bold;">', '{spanEnd}'=> '</span>']); ?></p>
                </div>
                <?php } ?>

            </div>
        <?php } ?>

    </div>

</form>
