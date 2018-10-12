<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">
	<input type="hidden" name="id" value="<?=$listing['id']; ?>">

    <div class="bar">
        <div class="bar__title"><?=$listing['title']; ?></div>
        <div class="bar__actions">
	        <a href="<?=sprintf(URL_LISTING, Format::slugify($listing['link'])); ?>" class="bar__action" target="_blank"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-view"></use></svg></a>
            <a href="/backend/listings/" class="bar__action"><svg class="icon"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-left-a"></use></svg></a>
        </div>
    </div>

	<div class="btns btns--stickyB">
    	<span class="R">
		    <button class="btn btn--positive" type="submit"><svg class="icon icon-check mar0"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use></svg> Save</button>
		</span>
    </div>

    <div class="block">

	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_TITLE'); ?>
			<em class="required">*</em></label>
		<input class="w1/1" type="text" name="title" value="<?=htmlspecialchars($listing['title']); ?>" size="98" required>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_LISTING_TITLE'); ?>
		</p>
	</div>
	<?php if ($listingAuth->canManageListings($authuser)) : ?>
	<div class="field">
		<p>Listed by <a href="<?=URL_BACKEND; ?>agents/agent/summary/?id=<?=$agent['id']; ?>">
			<?=Format::htmlspecialchars($agent['first_name']); ?>
			<?=Format::htmlspecialchars($agent['last_name']); ?>
			</a></p>
	</div>
	<?php endif; ?>
	<div class="field">
		<label class="field__label">Listing Link <em class="required">*</em></label>
		<input class="w1/1" id="listing-link" name="link" value="<?=Format::slugify($listing['link']); ?>" data-slugify required>
		<p class="text--mute"> To be used in your listing's URL Address. Use only lowercase alpha-numeric characters.
			<input class="w1/1" id="link-placeholder" value="<?=sprintf(URL_LISTING, Format::slugify($listing['link'])); ?>" readonly>
		</p>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_PRICE'); ?>
			<em class="required">*</em></label>
		<input class="w1/1" data-currency name="price" value="<?=preg_replace('/[^0-9]/', '', $listing['price']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_NUMBER'); ?>
		</label>
		<input class="w1/1" type="text" name="mls_number" value="<?=htmlspecialchars($listing['mls_number']); ?>">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_TYPE'); ?>
			<em class="required">*</em></label>
			<div class="input w1/1">
		<select class="w1/1" id="select-listing-types" name="type" required>
			<?php if (!empty($options['listing_types'])) : ?>
			<option value="">Choose One</option>
			<?php foreach ($options['listing_types'] as $option) : ?>
			<option value="<?=htmlspecialchars($option['value']); ?>"
				<?=!empty($option['required']) ? ' data-required="true"' : ''; ?>
				<?=($listing['type'] == $option['value']) ? ' selected="selected"' : ''; ?>
				>
			<?=Format::htmlspecialchars($option['title']); ?>
			</option>
			<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<a href="javascript:void(0);" class="action-manage btn" id="manage-listing-types">Manage</a>
		</div>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_STATUS'); ?>
			<em class="required">*</em></label>
			<div class="input w1/1">
		<select class="w1/1" id="select-listing-statuses" name="status" required>
			<?php if (!empty($options['listing_statuses'])) : ?>
			<option value="">Choose One</option>
			<?php foreach ($options['listing_statuses'] as $option) : ?>
			<option value="<?=htmlspecialchars($option['value']); ?>"
				<?=!empty($option['required']) ? ' data-required="true"' : ''; ?>
				<?=($listing['status'] == $option['value']) ? ' selected="selected"' : ''; ?>
				>
			<?=Format::htmlspecialchars($option['title']); ?>
			</option>
			<?php endforeach; ?>
			<?php endif; ?>
		</select>
		<a href="javascript:void(0);" class="action-manage btn" id="manage-listing-status">Manage</a>
		</div>
	</div>
	<?php if (!empty($teams)) { ?>
	<div class="field">
		<label class="field__label">Team</label>
		<div class="input w1/1">
			<select id="select-listing-teams" name="team">
				<option value="">Choose One</option>
				<?php foreach ($teams as $team) : ?>
				<option value="<?=htmlspecialchars($team->info('id')); ?>"
				<?=($listing['team'] == $team->info('id') ? ' selected="selected"' : ''); ?>
				>
				<?=Format::htmlspecialchars($team->info('name')); ?>
				</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<?php } ?>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_DESCRIPTION'); ?>
		</label>
		<textarea class="w1/1" name="description" cols="40" rows="8"><?=htmlspecialchars($listing['description']); ?></textarea>
	</div>
	<h3 class="divider">
        <span class="divider__label divider__label--left">Address / Location</span>
    </h3>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_STREET'); ?>
			<em class="required">*</em></label>
		<input class="w1/1" type="text" name="address" value="<?=htmlspecialchars($listing['address']); ?>" required>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_STATE'); ?>
			<em class="required">*</em></label>
		<select class="w1/1" name="state" id="select-locations" required>
			<option value="">Select a location</option>
			<?php if (!empty($locations)) : ?>
			<?php foreach ($locations as $country => $states) : ?>
			<optgroup label="<?=$country; ?>" value="<?=$country; ?>">
			<?php foreach ($states as $state) : ?>
			<option value="<?=$state['state']; ?>"<?=($state['state'] == $listing['state']) ? ' selected' : ''; ?>>
			<?=$state['state']; ?>
			</option>
			<?php endforeach; ?>
			</optgroup>
			<?php endforeach; ?>
			<?php endif; ?>
		</select>
    </div>
    <div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_CITY'); ?>
			<em class="required">*</em></label>
			<div class="input w1/1">
			<select name="city" required>
				<option value="">Select a location</option>
				<?php if (!empty($cities)) : ?>
				<?php foreach ($cities as $city) : ?>
				<option value="<?=$city['local']; ?>"
					<?=($city['user'] != 'Y') ? ' data-required="true"' : ''; ?>
					<?=($city['local'] == $listing['city']) ? ' selected' : ''; ?>
					>
				<?=Format::htmlspecialchars($city['local']); ?>
				</option>
				<?php endforeach; ?>
				<?php endif; ?>
			</select>
			<a href="javascript:void(0);" class="action-manage btn" id="manage-listing-cities">Manage</a>
		</div>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_ZIP'); ?>
			<em class="required">*</em></label>
		<input class="w1/1" type="text" name="zip" value="<?=htmlspecialchars($listing['zip']); ?>" size="12" required>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_SUBDIVISION'); ?>
		</label>
		<input class="w1/1" type="text" name="subdivision" value="<?=htmlspecialchars($listing['subdivision']); ?>">
	</div>
	<h3 class="divider">
        <span class="divider__label divider__label--left">Size &amp; Layout</span>
    </h3>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_BEDROOMS'); ?>
		</label>
		<input class="w1/1" type="number" name="bedrooms"  value="<?=htmlspecialchars($listing['bedrooms']); ?>" min=0>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_BATHROOMS'); ?>
		</label>
		<input class="w1/1" type="number" name="bathrooms"  value="<?=htmlspecialchars($listing['bathrooms']); ?>" min=0>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_HALFBATHS'); ?>
		</label>
		<input class="w1/1" type="number" name="bathrooms_half"  value="<?=htmlspecialchars($listing['bathrooms_half']); ?>" min=0>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_STORIES'); ?>
		</label>
		<input class="w1/1" type="number" name="stories"  value="<?=htmlspecialchars($listing['stories']); ?>" min=0>
	</div>
	<div class="field">
		<label class="field__label"># Of Garages</label>
		<input class="w1/1" type="text" name="garages" value="<?=htmlspecialchars($listing['garages']); ?>">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_SQ_FEET'); ?>
		</label>
		<input class="w1/1" type="number" name="squarefeet" value="<?=htmlspecialchars($listing['squarefeet']); ?>" min=0>
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_LOT_SIZE'); ?>
		</label>
		<input class="w1/1" type="text" name="lotsize" value="<?=htmlspecialchars($listing['lotsize']); ?>">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_BUILT'); ?>
		</label>
		<input class="w1/1" type="text" name="yearbuilt" id="yearbuilt" value="<?=htmlspecialchars($listing['yearbuilt']); ?>" pattern="^\d{1,4}$" maxlength="4">
    <div class="text--small text--mute">Format: YYYY</div>
	</div>
	<h3 class="divider">
        <span class="divider__label divider__label--left">Photo Manager</span>
    </h3>
    <p>Uploads must be in gif, jpeg, or png format and under 4 MB in size. Drag and drop listing photos to re-arrange.</p>
	<div class="field">
        <div data-uploader='<?=json_encode(['extraParams' => ['type' => 'listing', 'row' => (int) $listing['id']]]); ?>'>
			<?php if (!empty($uploads)) : ?>
			<div class="file-manager">
				<ul>
					<?php foreach ($uploads as $upload) : ?>
					<li>
						<div class="wrap"> <img src="/thumbs/300x300/uploads/<?=$upload['file']; ?>" border="0">
							<input type="hidden" name="uploads[]" value="<?=$upload['id']; ?>">
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php endif; ?>
		</div>
	</div>
	<h3 class="divider">
        <span class="divider__label divider__label--left">School Information</span>
    </h3>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_ELEM_SCHOOL'); ?>
		</label>
		<input class="w1/1" type="text" name="school_elementary" value="<?=htmlspecialchars($listing['school_elementary']); ?>">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_MID_SCHOOL'); ?>
		</label>
		<input class="w1/1" type="text" name="school_middle" value="<?=htmlspecialchars($listing['school_middle']); ?>">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_HIGH_SCHOOL'); ?>
		</label>
		<input class="w1/1" type="text" name="school_high" value="<?=htmlspecialchars($listing['school_high']); ?>">
	</div>
	<div class="field">
		<label class="field__label">School District</label>
		<input class="w1/1" type="text" name="school_district" value="<?=htmlspecialchars($listing['school_district']); ?>">
	</div>
	<h3 class="divider">
        <span class="divider__label divider__label--left">Advanced Information</span>
    </h3>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_LATITUDE'); ?>
		</label>
		<input class="w1/1" type="text" name="latitude" value="<?=htmlspecialchars($listing['latitude']); ?>" pattern="-?\d{1,3}\.\d+">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_LONGITUDE'); ?>
		</label>
		<input class="w1/1" type="text" name="longitude" value="<?=htmlspecialchars($listing['longitude']); ?>" pattern="-?\d{1,3}\.\d+">
	</div>
	<div class="field">
		<label class="field__label">
			<?=tpl_lang('LBL_FORM_LISTING_DIRECTIONS'); ?>
		</label>
		<textarea class="w1/1" name="directions" cols="20" rows="4"><?=htmlspecialchars($listing['directions']); ?></textarea>
	</div>
	<div class="field">
		<label class="field__label"><?=tpl_lang('LBL_FORM_LISTING_VIRTUAL_TOUR'); ?></label>
		<input class="w1/1" type="url" name="virtual_tour" value="<?=htmlspecialchars($listing['virtual_tour']); ?>" placeholder="http://" pattern="https?://.+">
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_LISTING_VIRTUAL_TOUR'); ?>
		</p>
	</div>
	<h3>
        <div class="divider">
            <span class="divider__label divider__label--left">Features</span>
        </div>
        <a href="javascript:void(0);" class="action-manage btn R" id="manage-listing-features" style="position: relative; top: -40px;">Manage</a>
    </h3>
    <div id="feature-list" data-options='<?=json_encode($options['listing_features'], JSON_HEX_APOS); ?>'>
		<?php if (!empty($options['listing_features'])) : ?>
		<?php foreach ($options['listing_features'] as $option) : ?>
		<label class="toggle toggle--stacked">
			<input type="checkbox" name="features[]" value="<?=htmlspecialchars($option['value']); ?>"<?=is_array($listing['features']) && in_array($option['value'], $listing['features']) ? ' checked="checked"' : ''; ?>>
			<span class="toggle__label"><?=$option['title']; ?></span>
		</label>
		<?php endforeach; ?>
		<?php endif; ?>
	</div>

</div>

</form>
