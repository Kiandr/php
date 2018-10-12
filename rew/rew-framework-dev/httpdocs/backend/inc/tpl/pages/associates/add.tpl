<div class="block">
<form action="?submit" method="post" class="rew_check">

	<div class="main-header">
		<h2><?= __('Add New ISA'); ?></h2>
		<div class="btns R">
			<a href="/backend/associates/" class="btn btn--ghost btn--ico timeline__back"><svg class="icon icon-left-a mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

	<div class="field">
		<label class="field__label"><?= __('Username'); ?> <em class="required">*</em></label>
		<input class="w1/1" name="username" value="<?=Format::htmlspecialchars($_POST['username']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Password'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="password" name="password" value="<?=Format::htmlspecialchars($_POST['password']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('First Name'); ?> <em class="required">*</em></label>
		<input class="w1/1" name="first_name" value="<?=Format::htmlspecialchars($_POST['first_name']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Last Name'); ?> <em class="required">*</em></label>
		<input class="w1/1" name="last_name" value="<?=Format::htmlspecialchars($_POST['last_name']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Email Address'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="email" name="email" value="<?=Format::htmlspecialchars($_POST['email']); ?>" required>
	</div>
	<h3><?= __('Phone Numbers'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Office Phone'); ?></label>
		<input class="w1/1" type="tel" name="office_phone" value="<?=Format::htmlspecialchars($_POST['office_phone']); ?>">
	</div>
	<div class="field">
		<label class="field__label"><?= __('Home Phone'); ?></label>
		<input class="w1/1" type="tel" name="home_phone" value="<?=Format::htmlspecialchars($_POST['home_phone']); ?>">
	</div>
	<div class="field">
		<label class="field__label"><?= __('Cell Phone'); ?></label>
		<input class="w1/1" type="tel" name="cell_phone" value="<?=Format::htmlspecialchars($_POST['cell_phone']); ?>">
	</div>
	<div class="field">
		<label class="field__label"><?= __('Fax'); ?></label>
		<input class="w1/1" type="tel" name="fax" value="<?=Format::htmlspecialchars($_POST['fax']); ?>">
	</div>
	<h3><?= __('Mailing Address'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Street Address'); ?></label>
		<input class="w1/1" name="address" value="<?=Format::htmlspecialchars($_POST['address']); ?>">
	</div>
	<div class="field">
		<label class="field__label"><?= __('City'); ?></label>
		<input class="w1/1" name="city" value="<?=Format::htmlspecialchars($_POST['city']); ?>">
	</div>
	<div class="field">
		<label class="field__label"><?= __('State / Province'); ?></label>
		<input class="w1/1" name="state" value="<?=Format::htmlspecialchars($_POST['state']); ?>">
	</div>
	<div class="field">
		<label class="field__label"><?= __('Zip / Postal'); ?></label>
		<input name="zip" value="<?=Format::htmlspecialchars($_POST['zip']); ?>">
	</div>
	<h3><?= __('Email Signature'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Signature'); ?></label>
		<textarea class="w1/1 tinymce email simple" id="signature" name="signature" rows="8" cols="85"><?=htmlspecialchars($_POST['signature']); ?>
</textarea>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Add Signature to Emails'); ?></label>
		<label class="boolean">
			<input type="radio" name="add_sig" value="Y"<?=($_POST['add_sig'] == 'Y') ? ' checked' : ''; ?>>
			<?= __('Yes'); ?></label>
		<label class="boolean">
			<input type="radio" name="add_sig" value="N"<?=($_POST['add_sig'] != 'Y') ? ' checked' : ''; ?>>
			<?= __('No'); ?></label>
	</div>
	<h3><?= __('Photo'); ?></h3>
    <div data-uploader='<?=json_encode(['multiple' => false, 'extraParams' => ['type' => 'associate']]); ?>'>
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
	<h3><?= __('Default Settings'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Filter'); ?></label>
		<select class="w1/1" name="default_filter">
			<?php foreach ($filters as $option) echo '<option value="' . $option['value'] . '"' . ($_POST['default_filter'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Sort By'); ?></label>
		<select class="w1/1" name="default_order">
			<?php foreach ($orders as $option) echo '<option value="' . $option['value'] . '"' . ($_POST['default_order'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Sort Order'); ?></label>
		<select class="w1/1" name="default_sort">
			<?php foreach ($sorts as $option) echo '<option value="' . $option['value'] . '"' . ($_POST['default_sort'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Timezone'); ?></label>
		<select class="w1/1" name="timezone">
			<?php foreach ($timezones as $option) echo '<option value="' . $option['value'] . '"' . ($_POST['timezone'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Page Limit'); ?></label>
		<select class="w1/1" name="page_limit">
			<?php foreach ($limits as $option) echo '<option value="' . $option['value'] . '"' . ($_POST['page_limit'] == $option['value'] ? ' selected': '') . '>' . $option['title'] . '</option>'; ?>
		</select>
	</div>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span> </div>
</form>
</div>