<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">
	<div class="bar">
    	<div class="bar__title">Website Settings</div>
	</div>
	<div class="block">

	<div class="field">
		<h2 class="panel__hd">Logo Settings</h2>
		<p class="text--mute"><?=UPLOAD_WARNING_LOGO; ?></p>
		<p class="text--mute"><?=UPLOAD_NOTE_LOGO; ?></p>
		<p class="text--mute"><?=UPLOAD_RETINA_NOTE_LOGO; ?></p>
	</div>
	<?php foreach ($logoSettings as $logoConstant => $logoData) { ?>
		<?php if ($logoData['use_uploader']) { ?>
		<div class="field">
			<h3 class="panel__hd"><?=__($logoData['title']); ?></h3>
			<p class="text--mute"><?=__($logoData['hint']); ?></p>
			<?php if (!empty(${$logoConstant})) { ?>
			<img src="<?=URL_UPLOADS . ${$logoConstant}; ?>" alt=""><br />
			<input type="hidden" name="<?=$logoConstant; ?>" value="<?=${$logoConstant}; ?>">
			<a class="btn btn--ghost delete" href="?deletePhoto&<?=$logoConstant; ?>&logoFile=<?=${$logoConstant}; ?>" onclick="return confirm('<?=__('Are you sure you want to remove the ' . $logoData['title'] . '?'); ?>');">
				<svg class="icon icon-trash mar0">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
				</svg>
			</a>
			<?php } ?>
		</div>
		<div class="field">
			<p><?=$logoData['recommended_size']; ?></p>
			<div class="field">
				<p>Upload for Regular Displays</p>
				<input class="w1/1" name="<?=$logoData['upload_field']; ?>" type="file">
			</div>
		</div>
		<div class="field">
			<?php if (!empty(${LOGO_RETINA . $logoConstant})) { ?>
			<img src="<?=URL_UPLOADS . ${LOGO_RETINA . $logoConstant}; ?>" alt=""><br />
			<input type="hidden" name="<?=LOGO_RETINA . $logoConstant; ?>" value="<?=${LOGO_RETINA . $logoConstant}; ?>">
			<a class="btn btn--ghost delete" href="?deletePhoto&<?=LOGO_RETINA . $logoConstant; ?>&logoFile=<?=${LOGO_RETINA . $logoConstant}; ?>" onclick="return confirm('<?=__('Are you sure you want to remove the ' . $logoData['title'] . ' for retina?'); ?>');">
				<svg class="icon icon-trash mar0">
					<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
				</svg>
			</a>
			<?php } ?>
		</div>
		<div class="field">
			<p>Upload for Retina Displays</p>
			<div class="field">
				<input class="w1/1" name="<?=$logoData['upload_field_retina']; ?>" type="file"<?=(empty(${$logoConstant}) ? ' title="You must first upload and save a corresponding regular display logo"' : ''); ?><?=(empty(${$logoConstant}) ? ' disabled' : ''); ?>>
			</div>
		</div>
		<?php } ?>
	<?php } ?>
    <div class="field">
        <div class="field">
            <h3 class="panel__hd"><?= __('Favicon'); ?></h3>
            <p class="text--mute">The format for the image is recommended to be 16x16 pixels or 32x32 pixels, using either 8-bit or 24-bit colors. The format of the image must be ICO, PNG, GIF.</p>
            <?php if (!empty($favicon)) { ?>
                <img src="/uploads/<?=$favicon; ?>" alt=""><br />
                <input type="hidden" name="favicon" value="<?=$favicon; ?>">
                <a class="btn btn--ghost delete" href="?deletePhoto&favicon" onclick="return confirm('<?= __('Are you sure you want to remove the favicon?'); ?>');">
                    <svg class="icon icon-trash mar0">
                        <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                    </svg>
                </a>
            <?php } ?>
        </div>
        <div class="field">
            <div class="field">
                <input class="w1/1" name="favicon_photo" type="file">
            </div>
        </div>
    </div>

	<?php
		/**
		 * LEC 2015 Settings
		 */
		if ($skin === 'lec-2015') {

		?>
	<h3>Mortgage Calculator Settings</h3>
	<div class="field">
		<label>Down Percentage</label>
		<input name="setting[<?=$settings_key; ?>][down_percent]" value="<?=htmlspecialchars($settings['down_percent']); ?>">
	</div>
	<div class="field">
		<label>Interest Rate</label>
		<select name="setting[<?=$settings_key; ?>][interest_rate]">
			<?php foreach ($interest_rates as $rate) {
			$selected = $settings['interest_rate'] == $rate ? ' selected' : '';
			echo '<option value="' . $rate . '"' . $selected . '>' . $rate . '%</option>';
			} ?>
		</select>
	</div>
	<div class="field">
		<label>Mortgage Term</label>
		<select name="setting[<?=$settings_key; ?>][mortgage_term]" class="text-center">
			<?php foreach ($mortgage_terms as $term) {
			$selected = $settings['mortgage_term'] == $term ? ' selected' : '';
			echo '<option value="' . $term . '"' . $selected . '>' . $term . ' Years</option>';
			} ?>
		</select>
	</div>
	<?php } ?>
	<?php if (Skin::hasFeature(Skin::AGENT_SPOTLIGHT) && !empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) { ?>
	<h3>Listing Details</h3>
	<div class="field">
		<label class="field__label">Agent Call-to-Action</label>
		<select class="w1/1" name="setting[<?=$settings_key; ?>][agent_id]">
			<?php
			// Turn Off
			echo '<option value=""' . (empty($settings['agent_id']) ? ' selected' : '') . '>-- Do Not Display --</option>';

			// Random Agent
			echo '<option value="RAND"' . ($settings['agent_id'] == 'RAND' ? ' selected' : '') . '>-- Use Random Agent -- </option>';

			// Available Agents
			if (!empty($agents) && is_array($agents)) {
				foreach ($agents as $agent) {
					echo '<option value="' . Format::htmlspecialchars($agent['id']) . '"' . ($settings['agent_id'] == $agent['id'] ? ' selected' : '') . '>' . Format::htmlspecialchars($agent['name']) . '</option>';
				}
			}

			?>
		</select>
	</div>
	<div class="field">
	<label class="field__label">Display Agent's Office #</label>
	<div>
		<input type="radio" name="setting[<?=$settings_key; ?>][agent_phone]" id="agent_phone_yes" value="1"<?=(!empty($settings['agent_phone']) ? ' checked' : ''); ?>>
		<label for="agent_phone_yes">Yes</label>
		<input type="radio" name="setting[<?=$settings_key; ?>][agent_phone]" id="agent_phone_no" value="0"<?=(empty($settings['agent_phone']) ? ' checked' : ''); ?>>
		<label for="agent_phone_no">No</label>
		</label>
	</div>
	<div class="field">
	<label class="field__label">Display Agent's Cell #</label>
	<div>
		<input type="radio" name="setting[<?=$settings_key; ?>][agent_cell]" id="agent_cell_yes" value="1"<?=(!empty($settings['agent_cell']) ? ' checked' : ''); ?>>
		<label for="agent_cell_yes">Yes</label>
		<input type="radio" name="setting[<?=$settings_key; ?>][agent_cell]" id="agent_cell_no" value="0"<?=(empty($settings['agent_cell']) ? ' checked' : ''); ?>>
		<label for="agent_cell_no">No</label>
		</label>
	</div>
	<?php } ?>
	<?php if (Skin::hasFeature(Skin::MORE_SEARCH_OPTIONS)) { ?>
	<h3>Search Results</h3>
	<div class="field">
		<label>Open More Options</label>
		<div>
			<input type="radio" name="setting[<?=$settings_key; ?>][more_options]" id="more_options_yes" value="1"<?=(!empty($settings['more_options']) ? ' checked' : ''); ?>>
			<label for="more_options_yes">Yes</label>
			<input type="radio" name="setting[<?=$settings_key; ?>][more_options]" id="more_options_no" value="0"<?=(empty($settings['more_options']) ? ' checked' : ''); ?>>
			<label for="more_options_no">No</label>
			</label>
		</div>
	</div>
	<?php } ?>
</div>

	<div class="btns btns--stickyB"> <span class="R">
		<button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
		</span>
	</div>
</form>