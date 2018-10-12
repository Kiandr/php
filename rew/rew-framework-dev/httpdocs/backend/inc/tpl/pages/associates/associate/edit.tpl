<form action="?submit" method="post" class="rew_check">
	<input type="hidden" name="id" value="<?=$associate['id']; ?>" />

<?php

include('inc/tpl/app/menu-associates.tpl.php');

// Render associate summary header (menu/title/preview)
echo $this->view->render('inc/tpl/partials/associate/summary.tpl.php', [
    'title' => __('Edit Associate'),
    'associate' => $associate,
    'associateAuth' => $associateAuth
]);
?>

<div class="btns btns--stickyB">
    <span class="R">
        <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
    </span>
</div>

<div class="block">

	<div class="field">
		<label class="field__label"><?= __('Username'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="username" value="<?=htmlspecialchars($associate['username']); ?>" required />
	</div>
	<p><a class="toggle_password" href="#"><?= __('Change Password'); ?></a></p>
	<input type="hidden" name="update_password" value=0 />
	<div id="update_password" class="hidden">
		<div class="cols">
			<div class="field col w1/2">
				<label class="field__label"><?= __('New Password'); ?> <em class="required">*</em></label>
				<input class="w1/1" type="password" name="new_password" value="" />
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Confirm Password'); ?> <em class="required">*</em></label>
				<input class="w1/1" type="password" name="confirm_password" value="" />
			</div>
		</div>
	</div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('First Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" name="first_name" value="<?=Format::htmlspecialchars($associate['first_name']); ?>" required />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Last Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" name="last_name" value="<?=Format::htmlspecialchars($associate['last_name']); ?>" required />
    	</div>
    </div>
	<div class="field">
		<label class="field__label"><?= __('Email Address'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="email" name="email" value="<?=Format::htmlspecialchars($associate['email']); ?>" required />
	</div>
	<h3><?= __('Phone Numbers'); ?></h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Office Phone'); ?></label>
    		<input class="w1/1" type="tel" name="office_phone" value="<?=Format::htmlspecialchars($associate['office_phone']); ?>" />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Home Phone'); ?></label>
    		<input class="w1/1" type="tel" name="home_phone" value="<?=Format::htmlspecialchars($associate['home_phone']); ?>" />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Cell Phone'); ?></label>
    		<input class="w1/1" type="tel" name="cell_phone" value="<?=Format::htmlspecialchars($associate['cell_phone']); ?>" />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Fax'); ?></label>
    		<input class="w1/1" type="tel" name="fax" value="<?=Format::htmlspecialchars($associate['fax']); ?>" />
    	</div>
    </div>
	<h3><?= __('Mailing Address'); ?></h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Street Address'); ?></label>
    		<input class="w1/1" name="address" value="<?=Format::htmlspecialchars($associate['address']); ?>" />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('City'); ?></label>
    		<input class="w1/1" name="city" value="<?=Format::htmlspecialchars($associate['city']); ?>" />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('State / Province'); ?></label>
    		<input class="w1/1" name="state" value="<?=Format::htmlspecialchars($associate['state']); ?>" />
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Zip / Postal'); ?></label>
    		<input class="w1/1" name="zip" value="<?=Format::htmlspecialchars($associate['zip']); ?>" />
    	</div>
    </div>
	<h3><?= __('Email Signature'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Signature'); ?></label>
		<textarea class="w1/1 tinymce email simple" id="signature" name="signature" rows="8" cols="85"><?=Format::htmlspecialchars($associate['signature']); ?></textarea>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Add Signature to Emails'); ?></label>
		<label class="toggle">
			<input type="radio" name="add_sig" value="Y"<?=($associate['add_sig'] == 'Y') ? ' checked' : ''; ?> />
			<span class="toggle__label"><?= __('Yes'); ?></span>
        </label>
		<label class="boolean">
			<input type="radio" name="add_sig" value="N"<?=($associate['add_sig'] != 'Y') ? ' checked' : ''; ?> />
			<span class="toggle__label"><?= __('No'); ?></span>
        </label>
	</div>
	<div class="field">
	<h2><?= __('Photo'); ?></h2>
        <div class="-marB" data-uploader='<?=json_encode(['multiple' => false, 'extraParams' => ['type' => 'associate', 'row' => (int) $associate['id']]]); ?>'>
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
    </div>
	<h3 class="panel__hd"><?= __('Default Settings'); ?></h3>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Filter'); ?></label>
    		<select class="w1/1" name="default_filter">
    			<?php foreach ($filters as $option) echo '<option value="' . $option['value'] . '"' . ($associate['default_filter'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Sort By'); ?></label>
    		<select class="w1/1" name="default_order">
    			<?php foreach ($orders as $option) echo '<option value="' . $option['value'] . '"' . ($associate['default_order'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Sort Order'); ?></label>
    		<select class="w1/1" name="default_sort">
    			<?php foreach ($sorts as $option) echo '<option value="' . $option['value'] . '"' . ($associate['default_sort'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    </div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Timezone'); ?></label>
    		<select class="w1/1" name="timezone">
    			<?php foreach ($timezones as $option) echo '<option value="' . $option['value'] . '"' . ($associate['timezone'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Page Limit'); ?></label>
    		<select class="w1/1" name="page_limit">
    			<?php foreach ($limits as $option) echo '<option value="' . $option['value'] . '"' . ($associate['page_limit'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
    		</select>
    	</div>
    </div>

	</div>

</form>