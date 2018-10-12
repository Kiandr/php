<div class="bar">
	<div class="bar__title"><?= __('Wise Agent'); ?></div>
	<div class="bar__actions">
		<?php if (isset($_GET['setup'])) { ?>
		<a class="bar__action" href="/backend/settings/partners/wiseagent/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
		<?php } else { ?>
		<a class="bar__action" href="/backend/settings/partners/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
        <?php } ?>
	</div>
</div>



<div class="block">

    <form action="<?=$form_action;?>" method="post" class="rew_check">

    	<div class="btns btns--stickyB"> <span class="R">
    		<?php if (isset($_GET['setup'])) { ?>
    		<?php } else if (empty($logins_valid)) { ?>
    		<?php } else { ?>
            <a href="?setup" class="btn settings"><?= __('Integration Settings'); ?></a>
    		<button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
    		<?php } ?>
    	</div>

    	<?php if (isset($_GET['setup'])) { ?>
    	<div class="field">
    		<label class="field__label"><?= __('API Key'); ?></label>
    		<input class="w1/1" type="text" name="wa_api_key" value="<?=htmlspecialchars($_POST['wa_api_key']); ?>">
    		<p class="text--mute"><?= __('You can find this in the Wise Agent account dashboard under Contacts &raquo; Leads.'); ?></p>
    	</div>

    	<div class="btns btns--stickyB">
    	    <span class="R">
    		    <button type="submit" class="btn btn--positive"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
    		</span>
        </div>

    	<?php } else if (empty($logins_valid)) { ?>
            <div class="help">
                <img src="/backend/img/hlp/setup.png"/>
                <h1><?= __('Set Up Wise Agent Integration'); ?></h1>
                <p> <?= __('Wise Agent integration is currently %s . To use this feature you must set up your Wise Agent API Key.', '<strong>' . __('inactive') .'</strong>'); ?></p>
                <a href="?setup" class="btn btn--positive"><svg class="icon icon-add mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg> <?= __('Set Up Wise Agent Integration'); ?></a>
            </div>
    		<?php  } else { ?>

        	<h2><?= __('Integration Status'); ?></h2>

        	<p>
                <?= __('Wise Agent integration is currently configured and active. Add leads to the new %s group to automatically push them to your Wise Agent account.',
                    '<label class="group_swatch group_<?=htmlspecialchars(Partner_WiseAgent::GROUP_STYLE);?> is-checked" style="float: none; display: inline-block;">' . __('Wise Agent') . '</label>'); ?>
            </p>


        	<h2 style="margin-bottom: 0;"><?= __('Lead Push Settings'); ?></h2>

    	<div class="field">
    		<label class="field__label"><?= __('Wise Agent Lead Category'); ?></label>
        	<input class="w1/1" type="text" name="wiseagent_category" value="<?=htmlspecialchars($authuser->info('partners.wiseagent.category')); ?>" placeholder="<?=htmlspecialchars(Partner_WiseAgent::DEFAULT_CATEGORY); ?>">
        	<label class="text--mute"><?= __('Determine the category that your leads will be assigned to in Wise Agent\'s contact manager. You can specify multiple categories by separating them with commas.'); ?></label>
        </div>

    	<div class="field">
    		<label class="field__label"><?= __('Add Leads to Wise Agent Call List'); ?></label>
        	<div>
        		<label class="toggle" for="call_list_true">
                    <input type="radio" id="call_list_true" name="call_list" value="true"<?=(($authuser->info('partners.wiseagent.call_list') == 'true') ? ' checked ' : ''); ?>>
                    <span class="toggle__label"><?= __('Yes'); ?></span>
                </label>
        		<label class="toggle" for="call_list_false">
                    <input type="radio" id="call_list_false" name="call_list" value="false"<?=(($authuser->info('partners.wiseagent.call_list') != 'true') ? ' checked ' : ''); ?>>
                    <span class="toggle__label"><?= __('No'); ?></span>
                </label>
        	</div>
        </div>

        <?php } ?>

		<?php if (isset($_GET['setup'])) { ?>
		<?php if (!empty($logins_valid)) { ?>
		<a class="btn delete" href="../?disconnect=wiseagent" onclick="javascript:return confirm('<?= __('Are you sure you want to disable the integration with this partner?'); ?>');"><?= __('Disable Integration'); ?></a>
		<?php } ?>
		<?php } ?>


    </form>
</div>