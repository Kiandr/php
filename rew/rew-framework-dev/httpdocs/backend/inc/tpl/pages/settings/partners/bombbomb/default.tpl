<form action="<?=$form_action;?>" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('BombBomb'); ?></div>
		<div class="bar__actions">
    		<?php if (isset($_GET['setup'])) { ?>
    		<a class="bar__action" href="/backend/settings/partners/bombbomb/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
    		<?php } else { ?>
			<a class="bar__action" href="/backend/settings/partners/"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
            <?php } ?>
		</div>
	</div>

<div class="block">

    <?php if (isset($_GET['setup'])) { ?>
	<div class="btns btns--stickyB">
    	<span class="R">
		    <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
        </span>
    </div>
    <?php } else if (empty($logins_valid)) { ?>
    <?php } else { ?>
	<div class="btns btns--stickyB">
    	<span class="R">
            <?php if (!isset($_GET['setup']) && !empty($logins_valid)) { ?>
                <a href="?setup" class="btn settings"><?= __('Integration Settings'); ?></a>
            <?php } ?>
		    <button class="btn btn--positive" type="submit"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-check"/></svg> <?= __('Save'); ?></button>
        </span>
    </div>
    <?php } ?>

	<?php if (isset($_GET['setup'])) { ?>
	<div class="field">
		<label class="field__label"><?= __('API Key'); ?></label>
		<input class="w1/1" type="text" name="api_key" value="<?=htmlspecialchars($_POST['api_key']); ?>">
		<label class="hint"><?= __('You can find this in the BombBomb account dashboard under My Profile &raquo; Integrations'); ?></label>
	</div>
	<?php } else if (empty($logins_valid)) { ?>
        <div class="help">
            <img src="/backend/img/hlp/setup.png"/>
            <h1><?= __('Set Up BombBomb Integration'); ?></h1>
            <p><?= __('BombBomb integration is currently %s. To use this feature you must provide your BombBomb API Key.', '<strong>' . __('inactive') . '</strong>'); ?> </p>
            <a href="?setup" class="btn btn--positive"><svg class="icon icon-add mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-add"></use></svg><?= __('Set Up BombBomb Integration'); ?></a>
        </div>
	<?php  } else { ?>

	<div class="divider">
	    <span class="divider__label divider__label--left text"><?= __('Sync Settings'); ?></span>
    </div>

        <div class="group_swatches partner">
            <p> <?= __('BombBomb integration is currently configured and active. Add leads to the new %s group to automatically sync them with your BombBomb account.',
                    '<label class="group_swatch group_<?=Partner_BombBomb::GROUP_STYLE;?> is-checked" style="float: none; display: inline-block;">' . __('BombBomb') . '</label>'); ?>
            </p>
        </div>

	<div class="field">
        <label class="field__label" for="list_id"><?= __('Destination List for synced leads'); ?></label>
    	<select class="w1/1" name="list_id" id="list_id">
    		<?php if (!empty($lists)) { ?>
    		<option value="">- None -</option>
    		<?php foreach ($lists as $list) { ?>
    		<?php $selected = $authuser->info('partners.bombbomb.list_id') === $list['id'] ? 'selected' : '';?>
    		<option <?=$selected;?> value="<?=$list['id'];?>">
    		<?=htmlspecialchars($list['name']);?>
    		(
    		<?=number_format($list['ContactCount']);?>
    		<?=Format::plural($list['ContactCount'], 'contacts', 'contact');?>
    		)</option>
    		<?php } ?>
    		<?php } else { ?>
    		<option value=""><?= __('No Lists found in BombBomb'); ?></option>
    		<?php } ?>
    	</select>
    </div>

        <label class="hint group_swatches">
            <?= __('Leads that are in the %s group will be automatically pushed to the BombBomb List you specify here',
                '<label class="group_swatch group_<?=Partner_BombBomb::GROUP_STYLE;?> is-checked" style="float: none; display: inline-block;">' . __('BombBomb') . '</label>'); ?>
        </label>

	<?php } ?>

    <?php if (!isset($_GET['setup']) && !empty($logins_valid)) { ?>
    <p>
        <a href="import/" class="btn"><?= __('Import Leads'); ?></a>
    </p>
    <?php } ?>

    <?php if (isset($_GET['setup']) && !empty($logins_valid)) { ?>
	<p><a class="btn delete" href="../?disconnect=bombbomb" onclick="javascript:return confirm('<?= __('Are you sure you want to disable the integration with this partner?'); ?>');"><?= __('Disable Integration'); ?></a></p>
    <?php } ?>


</div>

</form>