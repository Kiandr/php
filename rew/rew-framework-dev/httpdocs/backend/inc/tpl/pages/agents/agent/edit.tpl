<?php
    // Render agent summary header (menu/title/preview)
    echo $this->view->render('inc/tpl/partials/agent/summary.tpl.php', [
        'title' => sprintf('%s', $agent['id'] == $authuser->info('id') ? __('Preferences (Edit)') : __('Agent (Edit)')),
        'agent' => $agent,
        'agentAuth' => $agentAuth
    ]);
?>


<form action="?submit" method="post" enctype="multipart/form-data" class="rew_check">
	<input type="hidden" name="id" value="<?=$agent['id']; ?>">
    <input type="hidden" name="update_password" value=0>

	<div class="btns btns--stickyB">
        <span class="R">
		    <button class="btn btn--positive" type="submit">
                <svg class="icon icon-check mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-check"></use>
                </svg> <?= __('Save'); ?>
            </button>
		</span>
	</div>

<div class="block">

	<div class="field">
		<label class="field__label"><?= __('Username'); ?> <em class="required">*</em></label>
		<input class="w1/1" type="text" name="username" value="<?=htmlspecialchars($agent['username']); ?>"<?=($agent['id'] == '1') ? ' disabled' : ''; ?> required>
	</div>
	<p>
		<?=(($agent['id'] != '1') ? '<a href="javascript:void(0);" class="toggle_password">' . __('Change Password') . '</a>' : '');?>
	</p>
	<div id="update_password" class="hidden">
		<div class="cols">
			<div class="field col w1/2">
				<label class="field__label"><?= __('New Password'); ?> <em class="required">*</em></label>
				<input class="w1/1" type="password" name="new_password" value="">
			</div>
			<div class="field col w1/2">
				<label class="field__label"><?= __('Confirm Password'); ?> <em class="required">*</em></label>
				<input class="w1/1" type="password" name="confirm_password" value="">
			</div>
		</div>
	</div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('First Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="first_name" value="<?=htmlspecialchars($agent['first_name']); ?>" required>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Last Name'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="text" name="last_name" value="<?=htmlspecialchars($agent['last_name']); ?>" required>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Email Address'); ?> <em class="required">*</em></label>
    		<input class="w1/1" type="email" name="email" value="<?=htmlspecialchars($agent['email']); ?>" required>
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('SMS Email Address'); ?></label>
    		<input class="w1/1" type="email" name="sms_email" value="<?=htmlspecialchars($agent['sms_email']); ?>">
            <p class="text--mute">
                <?= __('Must contain the full 10 digit phone number followed by the cell providers SMS Gateway. (eg. 1234567890@sms.provider.net).'); ?>
                <br>
                <?= __('Enable or disable SMS notifications from the %s page.', '<a href="../notifications/?id=' . $agent['id'] . '">' . __('Notification Settings') . '</a>' ); ?>
            </p>
    	</div>
    </div>
	<?php if (!empty($offices)) : ?>
    <div class="field">
    	<label class="field__label"><?= __('Agent Office'); ?></label>
    	<select class="w1/1" name="office">
    		<option value=""><?= __('Select Office...'); ?></option>
    		<?php foreach ($offices as $office) : ?>
    		<option value="<?=$office['value']; ?>"<?=($agent['office'] == $office['value']) ? ' selected' : ''; ?>>
    		<?=$office['title']; ?>
    		</option>
    		<?php endforeach; ?>
    	</select>
    </div>
	<?php endif; ?>
	<?php $page->container(REW\Core\Interfaces\Definitions\Containers\AgentInterface::AFTER_BASIC_INFO)->loadModules(true, $agent->getRow()); ?>
	<h3><?= __('Phone Numbers'); ?></h3>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Office Phone'); ?></label>
    		<input class="w1/1" type="tel" name="office_phone" value="<?=htmlspecialchars($agent['office_phone']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Home Phone'); ?></label>
    		<input class="w1/1" type="tel" name="home_phone" value="<?=htmlspecialchars($agent['home_phone']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Cell Phone'); ?></label>
    		<input class="w1/1" type="tel" name="cell_phone" value="<?=htmlspecialchars($agent['cell_phone']); ?>">
    	</div>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Fax'); ?></label>
    		<input class="w1/1" type="tel" name="fax" value="<?=htmlspecialchars($agent['fax']); ?>">
    	</div>
    </div>
	<h3 class="panel__hd"><?= __('Agent Details'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Agent Bio'); ?></label>
		<textarea class="w1/1" name="remarks" rows="6" cols="85"><?=htmlspecialchars($agent['remarks']); ?></textarea>
	</div>
    <div class="cols">
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Professional Title'); ?></label>
    		<input class="w1/1" type="text" name="title" value="<?=htmlspecialchars($agent['title']); ?>">
    	</div>
    	<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER']) || !empty($_COMPLIANCE['backend']['always_show_idx_agent'])) : ?>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Website URL'); ?></label>
    		<input class="w1/1" type="url" name="website" value="<?=htmlspecialchars($agent['website']); ?>" placeholder="http://" pattern="https?://.+">
    	</div>
    	<?php if (!empty(Settings::getInstance()->IDX_FEEDS)) { ?>
    	<?php foreach (Settings::getInstance()->IDX_FEEDS as $feed => $settings) { ?>
    	<div class="field col w1/2">
    		<label class="field__label">
    			<?=$settings['title'];?>
    			<?= __('Agent ID'); ?></label>
    		<input class="w1/1" type="text" name="agent_id[<?=htmlspecialchars($feed);?>]" value="<?=htmlspecialchars($agent['agent_id'][$feed]);?>">
    	</div>
    	<?php } ?>
    	<?php } else { ?>
    	<div class="field col w1/2">
    		<label class="field__label"><?= __('Agent ID'); ?></label>
    		<input class="w1/1" type="text" name="agent_id[<?=htmlspecialchars(Settings::getInstance()->IDX_FEED);?>]" value="<?=htmlspecialchars($agent['agent_id'][Settings::getInstance()->IDX_FEED]); ?>">
    	</div>
	<?php } ?>
    </div>
	<?php endif; ?>
<?php if ($agentAuth->canManageApp($authuser)) { ?>
	<div class="field">
		<label class="field__label"><?= __('API Source'); ?></label>
		<select class="w1/1" name="api_source_id">
			<?php if (!empty($api_applications)) { ?>
			<option value=""><?= __('- None -'); ?></option>
			<?php foreach ($api_applications as $app) { ?>
			<?php $selected = $agent['auto_assign_app_id'] == $app['id'] ? 'selected' : '';?>
			<option <?=$selected;?> value="<?=$app['id'];?>">
			<?=htmlspecialchars($app['name']);?>
			</option>
			<?php } ?>
			<?php } else { ?>
			<option value="">- <?= __('No API Applications available'); ?> -</option>
			<?php } ?>
		</select>
        <p class="text--mute">
            <?= __(
                    'Pick an API application here in order to make leads created via the %s automatically get assigned to this agent. If multiple agents have the same API Source, leads from that source will be evenly assigned &amp; rotated between those agents.',
                    '<a href="' . URL_BACKEND . 'settings/api/">API</a>'
            ); ?>
        </p>
	</div>
	<?php } ?>

	<?php if (!empty($can_email)) : ?>
	<h3><?= __('Email Signature'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Signature'); ?></label>
		<textarea class="w1/1 tinymce email simple" id="signature" name="signature" rows="8" cols="85"><?=htmlspecialchars($agent['signature']); ?></textarea>
	</div>
	<div class="field">
		<label class="field__label"><?= __('Add Signature to Emails'); ?></label>
		<label class="toggle">
			<input type="radio" name="add_sig" value="Y"<?=($agent['add_sig'] == 'Y') ? ' checked' : ''; ?>>
			<span class="toggle__label"><?= __('Yes'); ?></span>
        </label>
		<label class="toggle">
			<input type="radio" name="add_sig" value="N"<?=($agent['add_sig'] != 'Y') ? ' checked' : ''; ?>>
			<span class="toggle__label"><?= __('No'); ?></span>
        </label>
	</div>
	<?php endif; ?>

	<?php if (!empty($networks)) { ?>
	<div id="social-media-section">
		<h2><?= __('Social Media'); ?></h2>
		<?php $even = true; foreach ($networks as $network) { ?>
			<div class="field">
				<label class="field__label"><?= Format::htmlspecialchars($network['name']) ?></label>
				<input type="text" name="<?= Format::slugify($network['form_field']); ?>" value="<?= Format::htmlspecialchars($network['url']); ?>" placeholder="http://" class="w1/1">
			</div>
		<?php } ?>
	</div>
	<?php } ?>

    <h3 class="panel__hd"><?= __('Agent Photo'); ?></h3>
    <div class="field">
        <input class="w1/1" name="agent_photo" type="file">
    </div>


    <?php if (!empty($agent['image'])) { ?>
        <div class="field">
            <input type="hidden" name="images" value="<?=$agent['image']; ?>">
            <img src="/thumbs/200x200/uploads/agents/<?=urlencode($agent['image']); ?>" alt=""><br />
            <a class="btn btn--ghost delete" href="?id=<?=$agent['id']; ?>&deletePhoto" onclick="return confirm('<?= __('Are you sure you want to remove this photo?'); ?>');">
                <svg class="icon icon-trash mar0">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="/backend/img/icos.svg#icon-trash"></use>
                </svg>
            </a>
        </div>
    <?php } ?>

	<h3 class="panel__hd"><?= __('Default Settings'); ?></h3>
    <div class="cols">
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Filter'); ?></label>
    		<select class="w1/1" name="default_filter">
                <?php if ($agentAuth->canEmailAgent()) { ?>
    				<option value="all"<?=($agent['default_filter'] == 'all') ? ' selected' : ''; ?>><?= __('All Leads'); ?></option>
    			<?php } ?>
    			<option value="my-leads"<?=($agent['default_filter'] == 'my-leads') ? ' selected' : ''; ?>><?= __('My Leads'); ?></option>
    			<option value="inquiries"<?=($agent['default_filter'] == 'inquiries') ? ' selected' : ''; ?>><?= __('Inquired'); ?></option>
    			<option value="pending"<?=($agent['default_filter'] == 'pending') ? ' selected' : ''; ?>><?= __('Pending'); ?></option>
    			<?php if ($agent->hasPermission($authuser::PERM_LEADS_ALL)) { ?>
    				<option value="unassigned"<?=($agent['default_filter'] == 'unassigned') ? ' selected' : ''; ?>><?= __('Unassigned'); ?></option>
    				<option value="rejected"<?=($agent['default_filter'] == 'rejected') ? ' selected' : ''; ?>><?= __('Rejected'); ?></option>
    			<?php } ?>
    			<option value="online"<?=($agent['default_filter'] == 'online') ? ' selected' : ''; ?>><?= __('Online'); ?></option>
    		</select>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Sort By'); ?></label>
    		<select class="w1/1" name=default_order>
    			<option value="score"<?=($agent['default_order'] == 'score') ? ' selected' : ''; ?>><?= __('Score'); ?></option>
    			<option value="value"<?=($agent['default_order'] == 'value') ? ' selected' : ''; ?>><?= __('Lead Value'); ?></option>
    			<option value="name"<?=($agent['default_order'] == 'name') ? ' selected' : ''; ?>><?= __('Name'); ?></option>
    			<option value="email"<?=($agent['default_order'] == 'email') ? ' selected' : ''; ?>><?= __('Email'); ?></option>
    			<option value="status"<?=($agent['default_order'] == 'status') ? ' selected' : ''; ?>><?= __('Status'); ?></option>
    			<option value="agent"<?=($agent['default_order'] == 'agent') ? ' selected' : ''; ?>><?= __('Agent'); ?></option>
    			<option value="lender"<?=($agent['default_order'] == 'lender') ? ' selected' : ''; ?>><?= __('Lender'); ?></option>
    			<option value="created"<?=($agent['default_order'] == 'created') ? ' selected' : ''; ?>><?= __('Date/Time Created'); ?></option>
    			<option value="active"<?=($agent['default_order'] == 'active') ? ' selected' : ''; ?>><?= __('Last Active'); ?></option>
    		</select>
    	</div>
    	<div class="field col w1/3">
    		<label class="field__label"><?= __('Sort Order'); ?></label>
    		<select class="w1/1" name="default_sort">
    			<option value="DESC"<?=($agent['default_sort'] == 'DESC') ? ' selected' : ''; ?>><?= __('Descending'); ?></option>
    			<option value="ASC"<?=($agent['default_sort'] == 'ASC') ? ' selected' : ''; ?>><?= __('Ascending'); ?></option>
    		</select>
    	</div>
    </div>
    <div class="cols">
        <div class="field col w1/2">
            <label class="field__label"><?= __('Timezone'); ?></label>
            <select class="w1/1" name="timezone">
                <?php foreach ($timezone as $zone) : ?>
                <option value="<?=$zone['id']; ?>"<?=($agent['timezone'] == $zone['id']) ? ' selected' : ''; ?>>
                <?=$zone['name']; ?>
                (GMT
                <?=$zone['gmt_off']; ?>
                )</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field col w1/2">
            <label class="field__label"><?= __('Page Limit'); ?></label>
            <select class="w1/1" name="page_limit">
                <option value="10"<?=($agent['page_limit'] == '10') ? ' selected' : ''; ?>>10</option>
                <option value="20"<?=($agent['page_limit'] == '20') ? ' selected' : ''; ?>>20</option>
                <option value="30"<?=($agent['page_limit'] == '30') ? ' selected' : ''; ?>>30</option>
                <option value="50"<?=($agent['page_limit'] == '50') ? ' selected' : ''; ?>>50</option>
                <option value="100"<?=($agent['page_limit'] == '100') ? ' selected' : ''; ?>>100</option>
            </select>
        </div>
    </div>

<?php if ($can_edit_admin_only_fields || ($agent['auto_assign_admin'] == 'true') || ($agent['auto_rotate'] == 'true')) { ?>

	<h3 class="panel__hd"><?= __('Lead Settings'); ?></h3>

    <?php $disabled = ($optout->isEnabled() && !$can_edit_admin_only_fields && $agent['auto_optout'] == 'true'); ?>
    <div class="field">
    	<label class="field__label"><?= __('Auto-Assign/Rotate Opt-In'); ?></label>
    	<div class="toggle">
    		<input type="radio" id="auto_assign_agent_true" name="auto_assign_agent" value="true"
    			<?=!empty($disabled) ? ' disabled' : ''; ?>
    			<?=($agent['auto_assign_agent'] == 'true') ? ' checked' : ''; ?>
    			>
    		<label class="toggle__label" for="auto_assign_agent_true"><?= __('Yes'); ?></label>
    		<input type="radio" id="auto_assign_agent_false" name="auto_assign_agent" value="false"
    			<?=!empty($disabled) ? ' disabled' : ''; ?>
    			<?=($agent['auto_assign_agent'] != 'true') ? ' checked' : ''; ?>
    			>
    		<label class="toggle__label" for="auto_assign_agent_false"><?= __('No'); ?></label>
    	</div>
    	<p class="text text--mute">
    		<?=tpl_lang('DESC_FORM_AUTO_ASSIGN_AGENT'); ?>
    	</p>
    </div>
    <?php if (!$can_edit_admin_only_fields && $disabled) { ?>
	<input type="hidden" name="auto_assign_agent" value="<?=$agent['auto_assign_agent']; ?>">
	<?php } ?>

    <?php if ($can_edit_admin_only_fields) { ?>
    <div class="field">
    	<label class="field__label"><?= __('Auto-Assign'); ?></label>
    	<div class="toggle">
    		<input type="radio" id="auto_assign_admin_true" name="auto_assign_admin" value="true"<?=($agent['auto_assign_admin'] == 'true') ? ' checked' : ''; ?>>
    		<label class="toggle__label" for="auto_assign_admin_true"><?= __('Yes'); ?></label>
    		<input type="radio" id="auto_assign_admin_false" name="auto_assign_admin" value="false"<?=($agent['auto_assign_admin'] != 'true') ? ' checked' : ''; ?>>
    		<label class="toggle__label" for="auto_assign_admin_false"><?= __('No'); ?></label>
    	</div>
    	<p class="text--mute">
    		<?=tpl_lang('DESC_FORM_AUTO_ASSIGN_ADMIN'); ?>
    	</p>
    </div>
    <div class="field">
    	<label class="field__label"><?= __('Auto-Rotate'); ?></label>
    	<div class="toggle">
    		<input type="radio" id="auto_rotate_true" name="auto_rotate" value="true"<?=($agent['auto_rotate'] == 'true') ? ' checked' : ''; ?>>
    		<label class="toggle__label" for="auto_rotate_true"><?= __('Yes'); ?></label>
    		<input type="radio" id="auto_rotate_false" name="auto_rotate" value="false"<?=($agent['auto_rotate'] != 'true') ? ' checked' : ''; ?>>
    		<label class="toggle__label" for="auto_rotate_false"><?= __('No'); ?></label>
    	</div>
    	<p class="text--mute">
    		<?=tpl_lang('DESC_FORM_AUTO_ROTATE'); ?>
    	</p>
    </div>
    <div class="field">
    	<label class="field__label"><?= __('Auto-Opt-Out'); ?></label>
    	<div class="toggle">
    		<input type="radio" id="auto_optout_true" name="auto_optout" value="true"
    			<?=($agent['auto_assign_admin'] != 'true' && $agent['auto_rotate'] != 'true') ? ' disabled' : ''; ?>
    			<?=($agent['auto_optout'] == 'true') ? ' checked' : ''; ?>
    			>
    		<label class="toggle__label" for="auto_optout_true"><?= __('Yes'); ?></label>
    		<input type="radio" id="auto_optout_false" name="auto_optout" value="false"
    			<?=($agent['auto_assign_admin'] != 'true' && $agent['auto_rotate'] != 'true') ? ' disabled' : ''; ?>
    			<?=($agent['auto_optout'] != 'true') ? ' checked' : ''; ?>
    			>
    		<label class="toggle__label" for="auto_optout_false"><?= __('No'); ?></label>
    	</div>
    	<p class="text--mute">
    		<?=tpl_lang('DESC_FORM_AUTO_OPTOUT'); ?>
    	</p>
    </div>
	<?php } ?>

    <div class="field">
        <label class="field__label"><?= __('Auto Generated Searches'); ?></label>
        <div class="toggle">
            <input type="radio" id="auto_search_true" name="auto_search" value="true"<?=($agent['auto_search'] == 'true') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="auto_search_true" class="boolean"><?= __('Yes'); ?></label>
            <input type="radio" id="auto_search_false" name="auto_search" value="false" <?=($agent['auto_search'] != 'true') ? ' checked' : ''; ?>>
            <label class="toggle__label" for="auto_search_false" class="boolean"><?= __('No'); ?></label>
        </div>
        <p class="text--mute"><?=tpl_lang('DESC_FORM_AUTO_SEARCH'); ?></p>
    </div>
	<?php } ?>

<?php if ($can_edit_admin_only_fields) : ?>
	<h3 class="panel__hd"><?= __('Agent Settings'); ?></h3>
	<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_MANAGER'])) { ?>
	<div class="field">
		<label class="field__label"><?= __('Show on Agents Page'); ?></label>
		<div class="toggle">
			<input id="settings_agent_page_true" type="radio" name="display" value="Y"<?=($agent['display'] == 'Y') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="settings_agent_page_true"> <?= __('Yes'); ?></label>
			<input id="settings_agent_page_false" type="radio" name="display" value="N"<?=($agent['display'] != 'Y') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="settings_agent_page_false"> <?= __('No'); ?></label>
		</div>
	</div>
	<?php } ?>

	<?php if (!empty(Settings::getInstance()->MODULES['REW_AGENT_SPOTLIGHT'])) { ?>
	<div class="field">
		<label class="field__label"><?= __('Display on Agents Feature'); ?></label>
		<div class="toggle">
			<input id="settings_agent_feature_true" type="radio" name="display_feature" value="Y"<?=($agent['display_feature'] == 'Y') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="settings_agent_feature_true"><?= __('Yes'); ?></label>
			<input id="settings_agent_feature_false" type="radio" name="display_feature" value="N"<?=($agent['display_feature'] != 'Y') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="settings_agent_feature_false"><?= __('No'); ?></label>
		</div>
	</div>
	<?php } ?>

    <?php if ($requiredToCancelSubdomains || ($subdomainAuth->canCreateSubdomains($authuser) && $agent['id'] != '1')) { ?>
	<div class="field">
		<label class="field__label"><?= __('Agent Website Enabled'); ?></label>
		<div class="toggle">
			<input id="settings_cms_true" type="radio" name="cms" value="true"<?=($agent['cms'] == 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="settings_cms_true"><?= __('Yes'); ?></label>
			<input id="settings_cms_false" type="radio" name="cms" value="false"<?=($agent['cms'] != 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="settings_cms_false"><?= __('No'); ?></label>
		</div>
	</div>
	<div id="agent-cms"<?=($agent['cms'] == 'true') ? '' : ' class="hidden"'; ?>>
		<? if ($agent['cms'] !== 'true') { ?>
		<div class="field">
			<label class="field__label"><?= __('Please provide the MLS board(s) which this agent is a member of:'); ?> *</label>
			<input type="hidden" name="mls_boards" value="">
			<ul style="list-style: none; padding: 0;">
				<?php foreach ($idx_feeds as $name => $title) { ?>
                    <?php $checked = isset($_POST['requested_feed'][$name]); ?>
				    <li class="-marB8">
                        <input type="checkbox" value="<?=$name; ?>" name="requested_feeds[<?=$name; ?>]" id="requested_feeds_<?=$name; ?>"<?=$checked ? ' checked' : ''; ?>>
                        <label class="boolean toggle__label" for="requested_feeds_<?=$name; ?>"><?=$title; ?></label>
                        <input name="feeds_agent[<?=$name; ?>]" placeholder="<?= __('Agent ID For %s', strtoupper($name)); ?>" value="<?=Format::htmlspecialchars($_POST['feed_agent'][$name]); ?>"<?=$checked ? ' required' : ' class="hidden"'; ?>>
                    </li>
				<?php } ?>
			</ul>
		</div>
		<? } ?>
		<div class="field">
			<label><?= __('Agent Website IDXs'); ?></label>
            <? if (Settings::isREW()) { ?>
                <p><?= __('This section is editable by REW staff only.  Check with our IDX team before enabling a specific feed for an agent website.'); ?></p>
            <? } else { ?>
                <p><?= __('This section is editable by REW staff only.  Please contact customer support to enable IDX access for this agent website.'); ?></p>
            <? } ?>
			<ul style="list-style: none; padding: 0;">
				<?php foreach ($idx_feeds as $name => $title) { ?>
				    <li class="-marB8">
                        <input type="checkbox" <?=Settings::isREW() ? "" : "disabled"?> value="<?=$name; ?>" name="feeds[]" id="settings_feeds_<?=$name; ?>" <?=in_array($name, $agent_idxs) ? 'checked' : ""; ?>>
                        <label class="boolean toggle__label" for="settings_feeds_<?=$name; ?>"><?=$title; ?></label>
                    </li>
				<?php } ?>
			</ul>
		</div>
        <div>
            <label class="-marB8 dB"><?=__('Agent Website Add-ons'); ?></label>
            <?php if (!empty($addons)) { ?>
                <ul style="list-style: none; padding: 0;">
                    <?php foreach ($addons as $addon) { ?>
                        <li class="-marB8">
                            <label>
                                <input type="checkbox" <?=(isset($agent['cms_addons']) && in_array($addon['db_val'], $agent['cms_addons'])) ? ' checked' : ''; ?> value="<?=$addon['db_val']; ?>" name="cms_addons[]">
                                <span class="toggle__label"><?=__($addon['title']); ?></span>
                            </label>
                        </li>
                    <?php } ?>
                </ul>
            <?php } else { ?>
                <div class="text--mute">No Add-ons Available</div>
            <?php } ?>
        </div>
		<div class="field">
			<label class="field__label"><?= __('Agent Link'); ?> <em>*</em></label>
			<input class="w1/1" name="cms_link" value="<?=$agent['cms_link']; ?>" data-link="<?=sprintf(URL_AGENT_SITE, '*'); ?>">
		</div>
		<div class="field">
			<label class="field__label"><?= __('Agent Site'); ?></label>
			<?php if (!empty($agent['cms_link'])) : ?>
			<a id="account-link" href="<?=sprintf(URL_AGENT_SITE, $agent['cms_link']); ?>" target="_blank">
			<?=sprintf(URL_AGENT_SITE, $agent['cms_link']); ?>
			</a>
			<?php else : ?>
			<a id="account-link" href="javascript:void(0);">
			<?=sprintf(URL_AGENT_SITE, '*'); ?>
			</a>
			<?php endif; ?>
		</div>
	</div>
	<?php } ?>
	<?php endif; ?>
	<?php if (!empty(Settings::getInstance()->MODULES['REW_REMAX_LAUNCHPAD'])) { ?>
	<h3 class="panel__hd"><?= __('SSO Settings'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('RE/MAX Launchpad Username'); ?></label>
		<input class="w1/1" type="text" name="remax_launchpad_username" value="<?=htmlspecialchars($agent['remax_launchpad_username']); ?>" maxlength="100">
		<p class="text--mute"><?= __('Setting up your RE/MAX Launchpad username here will allow you to directly access this REW backend account from the corresponding Launchpad account, without prompting you for your REW credentials.'); ?></p>
	</div>
	<?php } ?>
	<?php if (!empty(Settings::getInstance()->MODULES['REW_SHOWING_SUITE'])) { ?>
	<h3><?= __('Showing Suite Settings'); ?></h3>
	<div class="field">
		<label class="field__label"><?= __('Email Address'); ?></label>
		<input class="w1/1" type="email" name="showing_suite_email" value="<?=htmlspecialchars($agent['showing_suite_email']); ?>" placeholder="<?=htmlspecialchars($agent['email']); ?>">
		<p class="text--mute"><?= __('This is the email address that is tied to your Showing Suite account. You may leave this blank if it is the same as your agent account\'s email address.'); ?></p>
	</div>
	<?php } ?>
	<?php if (!empty($authorized_google) || !empty($authorized_microsoft)) { ?>
	<h3 class="panel__hd"><?= __('Calendar Settings'); ?></h3>
	<?php if (!empty($authorized_google)) { ?>
	<div class="field">
		<label class="field__label"><?= __('Google Calendar Push Enabled'); ?></label>
		<div class="toggle">
			<input id="google_calendar_sync_true" type="radio" name="google_calendar_sync" value="true"<?=($agent['google_calendar_sync'] == 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="google_calendar_sync_true"><?= __('Yes'); ?></label>
			<input id="google_calendar_sync_false" type="radio" name="google_calendar_sync" value="false"<?=($agent['google_calendar_sync'] != 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="google_calendar_sync_false"><?= __('No'); ?></label>
		</div>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_GOOGLE_CALENDAR'); ?>
		</p>
	</div>
	<?php } ?>
	<?php if (!empty($authorized_microsoft)) { ?>
	<div class="field">
		<label class="field__label"><?= __('Outlook Calendar Push Enabled'); ?></label>
		<div class="toggle">
			<input id="microsoft_calendar_sync_true" type="radio" name="microsoft_calendar_sync" value="true"<?=($agent['microsoft_calendar_sync'] == 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="microsoft_calendar_sync_true"><?= __('Yes'); ?></label>
			<input id="microsoft_calendar_sync_false" type="radio" name="microsoft_calendar_sync" value="false"<?=($agent['microsoft_calendar_sync'] != 'true') ? ' checked' : ''; ?>>
			<label class="toggle__label" for="microsoft_calendar_sync_false"><?= __('No'); ?></label>
		</div>
		<p class="text--mute">
			<?=tpl_lang('DESC_FORM_OUTLOOK_CALENDAR'); ?>
		</p>
	</div>
	<?php } ?>
	<?php } ?>
	<?php $page->container(REW\Core\Interfaces\Definitions\Containers\AgentInterface::AFTER_SETTINGS)->loadModules(true, $agent->getRow()); ?>
</div>

</form>
