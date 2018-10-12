<form action="<?=$form_action;?>" method="post" class="rew_check">

	<div class="bar">
		<div class="bar__title"><?= __('BombBomb'); ?></div>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/partners/bombbomb"><svg class="icon"><use xlink:href="/backend/img/icos.svg#icon-left-a"/></svg></a>
		</div>
	</div>

    <div class="block">

    	<div class="divider">
    	    <span class="divider__label divider__label--left text"><?= __('Import Leads'); ?></span>
        </div>

    	<?php if (!$importing) { ?>
            <div class="field" class="group_swatches partner">
                <p>
                    <?= __('This page allows you to import your existing REW leads into your BombBomb account. Note that you can import individual leads into BombBomb by adding them to the %s group. This tool is meant to be used for a one-time mass import.',
                        '<label class="group_swatch group_<?=Partner_BombBomb::GROUP_STYLE;?> is-checked" style="float: none; display: inline-block;">' . __('BombBomb') . '</label>'); ?>
                </p>
            </div>
            <div class="field">
                <label class="field__label" for="group_id"><?= __('Group to import leads from'); ?></label>
                <select class="w1/1" name="group_id" id="group_id">
                    <option value="">- <?= __('All My Leads'); ?> -</option>
                    <?php foreach ($groups as $group) { ?>
                        <?php $selected = $_POST['group_id'] === $group['id'] ? 'selected' : ''; ?>
                        <option <?= $selected; ?> value="<?= $group['id']; ?>">
                            <?= htmlspecialchars($group['name']); ?>
                            <?= n__('(%s lead)', '(%s leads)',number_format($group['leads']), number_format($group['leads'])); ?>
                        </option>
                    <?php } ?>
                </select>
            </div>
    	<?php } else { ?>
            <p><?= __('An import process is currently in progress. This tool will be available again once BombBomb finishes importing your leads.'); ?></p>
    	<?php } ?>
    	<?php if (!$importing) { ?>
    	<button type="submit" class="btn btn--positive"><?= __('Import Leads'); ?></button>
    	<?php } else { ?>
        <p><a href="?refresh" class="btn"><?= __('Refresh Status'); ?></a></p>
    	<?php } ?>


    </div>

</form>
