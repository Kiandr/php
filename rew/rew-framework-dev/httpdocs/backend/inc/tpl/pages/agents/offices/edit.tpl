<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">
	<input type="hidden" name="id" value="<?=$edit_office['id']; ?>">

	<div class="bar">
		<div class="bar__title"><?=$edit_office['title']; ?></div>
		<div class="bar__actions">
			<a class="bar__action" href="/backend/agents/offices/"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
		</div>
	</div>

<div class="block">

	<div class="field">
		<label class="field__label" for="title"><?= __('Office Title'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="title" value="<?=htmlspecialchars($edit_office['title']);  ?>" required>
		<p class="text--mute"><?= __('This is the name of the office and will be used as the heading.'); ?></p>
	</div>
	<div class="field">
		<label class="field__label" for="description"><?= __('Office Description'); ?></label>
		<textarea class="w1/1" rows="4" name="description"><?=htmlspecialchars($edit_office['description']); ?></textarea>
	</div>

	<h3 class="panel__hd"><?= __('Contact Details'); ?></h3>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label" for="email"><?= __('Email Address'); ?></label>
    		<input class="w1/1" type="email" name="email" value="<?=htmlspecialchars($edit_office['email']);  ?>">
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label" for="phone"><?= __('Phone Number'); ?></label>
    		<input class="w1/1" type="tel" name="phone" value="<?=htmlspecialchars($edit_office['phone']);  ?>">
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label" for="fax"><?= __('Fax'); ?></label>
    		<input class="w1/1" type="tel" name="fax" value="<?=htmlspecialchars($edit_office['fax']);  ?>">
    	</div>
    </div>
	<h3><?= __('Office Location'); ?></h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Street Address'); ?></label>
    		<input class="w1/1" type="text" name="address" value="<?=htmlspecialchars($edit_office['address']);  ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('City'); ?></label>
    		<input class="w1/1" type="text" name="city" value="<?=htmlspecialchars($edit_office['city']);  ?>">
    	</div>
        <div class="field col w1/2">
            <label class="field__label"><?= __('State / Province'); ?></label>
            <select class="w1/1" name="state" id="locationSelect">
                <option value=""><?= __('Select a location'); ?></option>
                <?php $country = ''; ?>
                <?php foreach ($states as $state) { ?>
                <?php if (empty($country)) { ?>
                <optgroup label="<?=$state['country']; ?>">
                <?php } ?>
                <?php if (!empty($country) && ($country != $state['country'])) { ?>
                </optgroup>
                <optgroup label="<?=$state['country']; ?>">
                <?php } ?>
                <?php $country = $state['country']; ?>
                <?php if (empty($state['title'])) continue; ?>
                <option value="<?=$state['value']; ?>"<?=($edit_office['state'] == $state['value']) ? ' selected="selected"' : ''; ?>>
                <?=$state['title']; ?>
                </option>
                <?php } ?>
                </optgroup>
            </select>
        </div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Zip / Postal Code'); ?></label>
    		<input class="w1/1" type="text" name="zip" value="<?=htmlspecialchars($edit_office['zip']);  ?>">
    	</div>
    </div>
	<?php $page->container(REW\Core\Interfaces\Definitions\Containers\OfficeInterface::AFTER_LOCATION)->loadModules(true, $edit_office); ?>
	<?php if (!empty(Settings::getInstance()->MODULES['REW_FEATURED_OFFICES'])) : ?>
	<div class="field">
		<h3 class="panel__hd"><?= __('Advanced Settings'); ?></h3>
		<?php $page->container(REW\Core\Interfaces\Definitions\Containers\OfficeInterface::BEFORE_ADVANCED)->loadModules(true, $edit_office); ?>
		<div class="field">
			<label class="field__label"><?= __('Display on Website'); ?></label>
			<div >
				<input id="display_true" type="radio" name="display" value="Y"<?=($edit_office['display'] == 'Y') ? ' checked="checked"' : ''; ?>>
				<label class="toggle__label" for="display_true"><?= __('Yes'); ?></label>
				<input id="display_false" type="radio" name="display" value="N"<?=($edit_office['display'] == 'N') ? ' checked="checked"' : ''; ?>>
				<label class="toggle__label" for="display_false"><?= __('No'); ?></label>
			</div>
		</div>
		<?php $page->container(REW\Core\Interfaces\Definitions\Containers\OfficeInterface::AFTER_ADVANCED)->loadModules(true, $edit_office); ?>
	</div>
	<?php endif; ?>
	<h3><?= __('Office Photo'); ?></h3>
	<?php if (!empty($edit_office['image'])) { ?>
	<div class="field"> <img src="<?=URL_OFFICE_IMAGES .  $edit_office['image']; ?>" border="0" width="90" height="65"><br>
		<input type="hidden" name="image" value="<?=$edit_office['image']; ?>">
		<a href="javascript:void(0)" class="remove_photo btn delete"><?= __('Delete'); ?></a> </div>
	<?php } ?>
	<div class="field">
		<input type="file" class="image_file w1/1" name="upload" value="">
	</div>
	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> <?= __('Save'); ?></button>
		</span>
    </div>

</div>

</form>