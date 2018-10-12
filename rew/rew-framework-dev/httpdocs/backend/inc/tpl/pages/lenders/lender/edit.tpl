<?php include('inc/tpl/app/menu-lenders.tpl.php'); ?>
<div class="bar">
	<a class="bar__title" data-drop="#menu--filters" href="javascript:void(0);">
        <?=Format::htmlspecialchars($lender['first_name'] . ' ' . $lender['last_name']); ?> (<?= __('Edit'); ?>)
        <svg class="icon icon-drop">
            <use xlink:href="/backend/img/icos.svg#icon-drop" xmlns:xlink="http://www.w3.org/1999/xlink"/>
        </svg>
	</a>
	<div class="bar__actions">
		<a class="bar__action" href="<?=URL_BACKEND; ?>lenders/">
            <a class="bar__action" href="<?=URL_BACKEND; ?>lenders/lender/summary/?id=<?=$lender['id']; ?>" class="btn btn--ghost timeline__back"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </a>
	</div>
</div>
<?php include('inc/tpl/app/summary-lender.tpl.php'); ?>



<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$lender['id']; ?>">

    <div class="block">

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span> </div>
	<h3 class="divider">
        <span class="divider__label divider__label--left"><?= __('Lender Information'); ?></span>
    </h3>
	<div class="field">
		<label class="field__label"><?= __('Username'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="username" value="<?=htmlspecialchars($lender['username']); ?>" required>
	</div>
	<p><a class="toggle_password" style="cursor: pointer;"><?= __('Change Password'); ?></a></p>
	<input type="hidden" name="update_password" value=0>
	<div id="update_password" class="cols hidden">
		<div class="field col w1/2">
			<label class="field__label"><?= __('New Password'); ?> <em class="required">*</em></label>
			<input class="w1/1" type="password" name="new_password" value="">
		</div>
		<div class="field col w1/2">
			<label class="field__label"><?= __('Confirm Password'); ?> <em class="required">*</em></label>
			<input class="w1/1" type="password" name="confirm_password" value="">
		</div>
	</div>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('First Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" name="first_name" value="<?=Format::htmlspecialchars($lender['first_name']); ?>" required>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Last Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" name="last_name" value="<?=Format::htmlspecialchars($lender['last_name']); ?>" required>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Email Address'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="email" name="email" value="<?=Format::htmlspecialchars($lender['email']); ?>" required>
    	</div>
    </div>
    <h3 class="divider">
        <span class="divider__label divider__label--left"><?= __('Phone Number'); ?>s</span>
    </h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Office Phone'); ?></label>
    		<input class="w1/1" type="tel" name="office_phone" value="<?=Format::htmlspecialchars($lender['office_phone']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Home Phone'); ?></label>
    		<input class="w1/1" type="tel" name="home_phone" value="<?=Format::htmlspecialchars($lender['home_phone']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Cell Phone'); ?></label>
    		<input class="w1/1" type="tel" name="cell_phone" value="<?=Format::htmlspecialchars($lender['cell_phone']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Fax'); ?></label>
    		<input class="w1/1" type="tel" name="fax" value="<?=Format::htmlspecialchars($lender['fax']); ?>">
    	</div>
    </div>
    <h3 class="divider">
        <span class="divider__label divider__label--left"><?= __('Mailing Address'); ?></span>
    </h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Street Address'); ?></label>
    		<input class="w1/1" name="address" value="<?=Format::htmlspecialchars($lender['address']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('City'); ?></label>
    		<input class="w1/1" name="city" value="<?=Format::htmlspecialchars($lender['city']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('State / Province'); ?></label>
    		<input class="w1/1" name="state" value="<?=Format::htmlspecialchars($lender['state']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Zip / Postal'); ?></label>
    		<input class="w1/1" name="zip" value="<?=Format::htmlspecialchars($lender['zip']); ?>">
    	</div>
    </div>
    <h3 class="divider">
        <span class="divider__label divider__label--left"><?= __('Lender Photo'); ?></span>
    </h3>
    <div class="-marB" data-uploader='<?=json_encode(['multiple' => false, 'extraParams' => ['type' => 'lender', 'row' => (int) $lender['id']]]); ?>'>
		<?php if (!empty($uploads)) { ?>
		<div class="file-manager">
			<ul>
				<?php foreach ($uploads as $upload) { ?>
				<li>
					<div class="wrap"> <img src="/thumbs/95x95/uploads/<?=$upload['file']; ?>" border="0">
						<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
					</div>
				</li>
				<?php } ?>
			</ul>
		</div>
		<?php } ?>
	</div>
	<h3 class="divider" style="margin-top: 40px;">
        <span class="divider__label divider__label--left"><?= __('Default Settings'); ?></span>
    </h3>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Filter'); ?></label>
    		<select class="w1/1" name="default_filter">
    			<?php foreach ($filters as $option) echo '<option value="' . $option['value'] . '"' . ($lender['default_filter'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Sort By'); ?></label>
    		<select class="w1/1" name="default_order">
    			<?php foreach ($orders as $option) echo '<option value="' . $option['value'] . '"' . ($lender['default_order'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Sort Order'); ?></label>
    		<select class="w1/1" name="default_sort">
    			<?php foreach ($sorts as $option) echo '<option value="' . $option['value'] . '"' . ($lender['default_sort'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    </div>
	<div class="field">
		<label class="field__label"><?= __('Timezone'); ?></label>
		<select class="w1/1" name="timezone">
			<?php foreach ($timezones as $option) echo '<option value="' . $option['value'] . '"' . ($lender['timezone'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Page Limit'); ?></label>
		<select class="w1/1" name="page_limit">
			<?php foreach ($limits as $option) echo '<option value="' . $option['value'] . '"' . ($lender['page_limit'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<?php if ($lendersAuth->canManageLenders($authuser) || $lender['auto_assign_admin'] == 'true') { ?>
	<h3 class="divider">
        <span class="divider__label divider__label--left"><?= __('Lender Settings'); ?></span>
    </h3>
        <?php if ($lendersAuth->canManageLenders($authuser)) { ?>
	<div class="field">
		<label class="field__label"><?= __('Auto-Assign'); ?></label>
		<div class="toggle">
			<input type="radio" id="auto_assign_admin_true" name="auto_assign_admin" value="true"<?=($lender['auto_assign_admin'] != 'false') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="auto_assign_admin_true"><?= __('Yes'); ?></label>
			<input type="radio" id="auto_assign_admin_false" name="auto_assign_admin" value="false"<?=($lender['auto_assign_admin'] == 'false') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="auto_assign_admin_false"><?= __('No'); ?></label>
		</div>
	</div>
	<?php } ?>
	<div class="field">
		<label class="field__label"><?= __('Auto-Assign Opt-In'); ?></label>
		<div class="toggle">
			<input type="radio" id="auto_assign_optin_true" name="auto_assign_optin" value="true"<?=($lender['auto_assign_optin'] != 'false') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="auto_assign_optin_true"><?= __('Yes'); ?></label>
			<input type="radio" id="auto_assign_optin_false" name="auto_assign_optin" value="false"<?=($lender['auto_assign_optin'] == 'false') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="auto_assign_optin_false"><?= __('No'); ?></label>
		</div>
	</div>
	<?php } ?>
	<?php $page->container(REW\Core\Interfaces\Definitions\Containers\LenderInterface::AFTER_SETTINGS)->loadModules(true, $lender->getRow()); ?>

    </div>

</form>